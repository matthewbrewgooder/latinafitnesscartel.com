const init_admin_select = function(){

	const $ = jQuery
	const generate_destination_language = () => {
		return weglot_languages.available.filter(itm => {
			return itm.code !== weglot_languages.original
		});
	}

	let destination_selectize

	const execute = () => {
		$(".weglot-select-original").selectize({
			delimiter: "|",
			persist: false,
			valueField: "code",
			labelField: "local",
			searchField: ["code", "english", "local"],
			sortField: [
				{ field: "code", direction: "asc" },
				{ field: "english", direction: "asc" }
			],
			maxItems: 1,
			plugins: ["remove_button"],
			options: weglot_languages.available,
			onChange: value => {
				if(value.length > 0){
					destination_selectize.data('selectize').clearOptions()

					destination_selectize
						.data("selectize")
						.addOption(weglot_languages.available.filter(itm => {
							return itm.code !== value
						}));
				}
			}
		});


		destination_selectize = $(".weglot-select-destination").selectize(
			{
				delimiter: "|",
				persist: false,
				valueField: "code",
				labelField: "local",
				searchField: ["code", "english", "local"],
				sortField: [
					{ field: "code", direction: "asc" },
					{ field: "english", direction: "asc" }
				],
				maxItems: weglot_languages.limit,
				plugins: ["remove_button"],
				options: generate_destination_language(),
				render: {
					option: function(item, escape) {
						return (
							'<div class="weglot__choice__language">' +
							'<span class="weglot__choice__language--local">' +
							escape(item.local) +
							"</span>" +
							'<span class="weglot__choice__language--english">' +
							escape(item.english) +
							" [" +
							escape(item.code) +
							"]</span>" +
							"</div>"
						);
					}
				}
			}
		);

	}

	document.addEventListener('DOMContentLoaded', () => {
		execute();
	})
}

export default init_admin_select;

