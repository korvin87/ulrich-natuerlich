<?php
namespace Deployer;

require 'recipe/typo3.php';
require 'contrib/rsync.php';
require 'contrib/cachetool.php';

set('typo3_webroot', 'web');
set('keep_releases', 5);
set('application', 'ulrich-natuerlich');
set('ssh_multiplexing', true);

set('rsync_src', function () {
    return __DIR__; // If your project isn't in the root, you'll need to change this.
});

set('release_name', function () {
    return (string) run('date +"%Y-%m-%d_%H-%M-%S"');
});

/**
 * Shared dirs
 */
add('shared_dirs', [
    '{{typo3_webroot}}/fileadmin',
    '{{typo3_webroot}}/typo3temp',
    '{{typo3_webroot}}/uploads',
    'var'
]);

set('shared_files', array(
    '{{typo3_webroot}}/.htaccess',
    '{{typo3_webroot}}/robots.txt',
    '{{typo3_webroot}}/typo3conf/AdditionalConfiguration.php',
));

/**
 * Rsync contribution
 */
add('rsync', [
    'exclude' => [
        '.ddev',
        '.git',
        '.gitattributes',
        '.gitignore',
        '.idea',
        'hosts.yml',
        'deploy.php',
        'readme.md',
        './frontend',
        'sql',
    ],
    'options' => [
        'links',
    ]
]);

// Writable dirs by web server
add('writable_dirs', []);
set('allow_anonymous_stats', false);

/**
 * Advance Typo3 tasks
 */
task('typo3:clearCacheAll', function () {
    run('cd {{release_path}} && {{bin/php}} vendor/bin/typo3cms cache:flush');
})->desc('Clear TYPO3 caches');

task('typo3:updateSchema', function () {
    run('cd {{release_path}} && {{bin/php}} vendor/bin/typo3cms database:updateschema');
})->desc('Update TYPO3 database schema');

task('typo3:fixFoldersStructure', function () {
    run('cd {{release_path}} && {{bin/php}} vendor/bin/typo3cms install:fixfolderstructure');
})->desc('Run TYPO3 fix folders structure');

task('typo3:languageUpdate', function () {
    cd('{{release_path}}');
    run('cd {{release_path}} && {{bin/php}} vendor/bin/typo3cms language:update');
})->desc('Update TYPO3 language files');

import('hosts.yml');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// [Optional] if deploy is successful show
after('deploy', 'deploy:success');

/**
 * Main TYPO3 task
 */
task('deploy', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:shared',
    'deploy:symlink',
    'deploy:unlock',
    'typo3:updateSchema',
    'typo3:languageUpdate',
    'typo3:fixFoldersStructure',
    'typo3:clearCacheAll',
    'deploy:cleanup',
])->desc('Deploy Process');