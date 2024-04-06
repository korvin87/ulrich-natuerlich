mod {
	wizards.newContentElement.wizardItems {
		plugins {
			elements {
				plugins_abavo_search_pilist {
					iconIdentifier = wizard-abavo_search
					title = LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:mod.wizards.newContentElement.pisearch
					description = LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:mod.wizards.newContentElement.pisearch.description
					tt_content_defValues {
						CType = list
						list_type = abavosearch_pisearch
					}
				}
				plugins_abavo_search_piteaser {
					iconIdentifier = wizard-abavo_search
					title = LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:mod.wizards.newContentElement.piresults
					description = LLL:EXT:abavo_search/Resources/Private/Language/locallang_db.xlf:mod.wizards.newContentElement.piresults.description
					tt_content_defValues {
						CType = list
						list_type = abavosearch_piresults
					}
				}
			}
			show = *
		}
	}
}