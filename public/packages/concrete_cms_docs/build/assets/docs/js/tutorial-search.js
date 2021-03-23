/**
 * @project:   ConcreteCMS Docs
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

$(function () {
    var $form = $(".tutorial-search-form");

    $form.find("select[name=searchField]")
        .selectpicker({
            liveSearch: true
        })
        .ajaxSelectPicker({
            ajax: {
                method: 'GET',
                data: {
                    q: '{{{q}}}'
                }
            },
            preprocessData: function (results) {
                var options = [];

                for (var result of results) {
                    var icon = "";

                    switch (result.type) {
                        case 'question':
                            icon = 'fas fa-question';
                            break;
                        case 'tag':
                            icon = 'fas fa-tag';
                            break;
                        case 'query':
                            icon = 'fas fa-search';
                            break;
                    }

                    options.push(
                        {
                            'value': result.id,
                            'text': result.text,
                            'data': {
                                'icon': icon,
                                'subtext': result.type
                            },
                            'disabled': false
                        }
                    );
                }

                return options;
            },
            preserveSelected: false
        })
        .on("changed.bs.select", function () {
            $form.find("input[name=search]").val(this.value);
            $form.submit();
        });
});