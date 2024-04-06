<?php
/**
 * ulrich_products - ContactController.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 19.06.2018 - 07:52:46
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\UlrichProducts\Controller;

use Psr\Http\Message\ResponseInterface;
use Abavo\UlrichProducts\Domain\Repository\ContactRepository;
/**
 * ContactController
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
class ContactController extends ActionController
{
    /**
     * ContactRepository
     *
     * @var ContactRepository
     */
    protected $contactRepository = null;

    /**
     * The list action
     */
    public function listAction(): ResponseInterface
    {
        $this->variables['contacts'] = ((boolean) $this->settings['contacts']) ? $this->contactRepository->findByUids($this->settings['contacts']) : $this->contactRepository->findAll();
        $this->view->assignMultiple($this->variables);
        return $this->htmlResponse();
    }

    public function injectContactRepository(ContactRepository $contactRepository): void
    {
        $this->contactRepository = $contactRepository;
    }
}