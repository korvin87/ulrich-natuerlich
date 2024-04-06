<?php
/**
 * abavo_search - ModifyItemInterface.php
 * 
 * @author: Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 * @since: 08.06.2018 - 11:01:45
 * 
 * @copyright: since 2018 - abavo GmbH <dev(at)abavo.de>
 * @license: Proprietary
 */

namespace Abavo\AbavoSearch\Domain\Api;

/**
 * DModifyItemInterface
 *
 * @author Mathias Bruckmoser <mathias.bruckmoser(at)abavo.de>
 */
interface ModifyItemInterface
{

    public function modfiyItem(&$item);
}