mod {
	wizards.newContentElement.wizardItems {
		plugins {
			elements {
				plugins_abavo_form_pi {
					iconIdentifier = wizard-abavo_form
					title = LLL:EXT:abavo_form/Resources/Private/Language/locallang_db.xlf:mod.wizards.newContentElement.pi
					description = LLL:EXT:abavo_form/Resources/Private/Language/locallang_db.xlf:mod.wizards.newContentElement.pi.description
					tt_content_defValues {
						CType = list
						list_type = abavoform_pi
					}
				}
			}
			show = *
		}
	}
}