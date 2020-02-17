/**
 * Created by lucius on 23.08.16.
 */

(function ($) {
    $(function (e) {

        function addTypeaheadMultipleRemote(element, options_url, options_template) {
            if ($(element).is('input[type="hidden"],input[type="checkbox"],input[type="button"] ')) {
                return;
            }
            var values = new Bloodhound({
                datumTokenizer: function (datum) {
                    var tokens = [];
                    if (typeof(datum) == 'object') {
                        for (var i in datum) {
                            tokens.push(datum[i] + "");
                        }
                    } else {
                        tokens = [datum];
                    }
                    return tokens;
                },
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                prefetch: {
                    url: options_url,
                    cache: false
                }
            });
            values.initialize();
            //eval("templatefunction = "+options_template+";");
            $(element).typeahead({

                    minLength: 0,
                    highlight: true,
                    limit: 10000,
                }, {
                name: $(this).attr('id'),
                    source: function (q, sync) {
                        if (q === '' || q === '*') {
                            sync(values.index.all());
                        }

                        else {

                            values.search(q, sync);
                        }
                    },
                    //display:'postemail',
                    limit: 10000,
                    templates: {
                        suggestion: function (data) {
                            var localtemplate = options_template;
                            for (var i in data) {
                                localtemplate = localtemplate.replace(new RegExp("\{\{+" + i + "\}\}", "g"), data[i]);
                            }
                            return localtemplate;
                        },
                    },

                }
            )
                .on('typeahead:select', function (e, object) {
                    var row = $(this).closest('div.row.subentityrowedit');
                    for (var i in object) {
                        row.find('[name*="[' + i + ']"]').val(object[i]);
                        row.find('[name*="[' + i + ']"]').typeahead('val', object[i]);
                    }
                    return false;
                });
        }

        function add_remove_on_click_handler() {
            $('button.btn.bacluc-inlineform.actionbutton.delete').off("click");
            $('button.btn.bacluc-inlineform.actionbutton.delete').click(function (e) {
                $(this)
                //div.subentityrowedit.row
                    .parent()
                    //div.col-xs-12 (foreach entity row)
                    .parent().remove();
            });
        }


        $('button.btn.bacluc-inlineform.actionbutton.add').click(function (e) {
            var hiddenrow = $(this).parent().find('div.hidden_form_row');
            var hiddenrowcopy = $($(this).parent().find('div.hidden_form_row').html());
            var parentpostname = $(hiddenrow).find('div.parent_postname').text();
            var parentidname = $(hiddenrow).find('div.parent_idname').text();
            var replace_brace_in_id_with = $(hiddenrow).find('div.replace_brace_in_id_with').text();
            var rownum = $(hiddenrow).find('div.rownum').text();
            var prepend_before_realname = $(hiddenrow).find('div.prepended_before_realname').text();
            var options_url = $(hiddenrow).find('div.options_url').text();
            var options_template = $(hiddenrow).find('div.options_template').html();
            rownum = parseInt(rownum);

            //update the old rownum
            $(hiddenrow).find('div.rownum').text(rownum + 1);

            /**
             *
             * @param parentpostname
             * @param rownum
             * @param prependet_before_realname
             * @param currentpostname
             * @returns {string}
             */
            function renameAttribute(parentpostname, rownum, prependet_before_realname, currentpostname, replace_brace_in_id_with) {

                var newprepend = parentpostname + "[" + rownum + "][";


                var postname = currentpostname.substr(prependet_before_realname.length);


                var returnstring = newprepend + postname + "]";
                if (replace_brace_in_id_with === undefined && replace_brace_in_id_with !== "") {
                    return returnstring;
                }
                return returnstring.replace(new RegExp("(\\\[|\\\])", "g"), replace_brace_in_id_with);

            }

            //now rename id and name of new row


            $(hiddenrowcopy).find('*').each(function (e) {


                if ($(this).is('[id]')) {
                    $(this).attr('id', renameAttribute(parentpostname, rownum, prepend_before_realname, $(this).attr('id'), replace_brace_in_id_with));
                }
                if ($(this).is('label[for]')) {
                    $(this).attr('for', renameAttribute(parentpostname, rownum, prepend_before_realname, $(this).attr('for')));
                }
                if (($(this).is('input[name]') || $(this).is('textarea[name]') || $(this).is('select[name]') ) && true) {
                    $(this).attr('name', renameAttribute(parentpostname, rownum, prepend_before_realname, $(this).attr('name')));
                    if (window.parsley) {
                        $(this).on('change, keyup', function (e) {
                            $(this).parsley().validate();
                        });
                    }
                    if (($(this).is('input[name]') || $(this).is('textarea[name]')) && !$(this).is('input[type="hidden"]')) {
                        if ($(this).hasClass('date') || $(this).parent().hasClass('date')) {
                            if ($(this).parent().hasClass('date')) {
                                $(this).datepicker({format: $(this).parent().attr('data-date-format')});
                            } else {
                                $(this).datepicker({format: $(this).attr('data-date-format')});
                            }

                        } else {

                            addTypeaheadMultipleRemote(this, options_url, options_template);
                        }
                    }
                }


            });
            $(hiddenrow).parent().find('div.bacluc-realrows ').append(hiddenrowcopy);
            add_remove_on_click_handler();
        });

        add_remove_on_click_handler();


        //go through the single DirectEdit Areas and add autocompletion there:
        $('div.subentityedit').each(function () {



            //first read information

            var parentpostname = $(this).find('div.parent_postname.hiddenforminfo').text();
            var parentidname = $(this).find('div.parent_idname.hiddenforminfo').text();
            var replace_brace_in_id_with = $(this).find('div.replace_brace_in_id_with.hiddenforminfo').text();
            var prepend_before_realname = $(this).find('div.prepended_before_realname.hiddenforminfo').text();
            var options_url = $(this).find('div.options_url.hiddenforminfo').text();
            var options_template = $(this).find('div.options_template.hiddenforminfo').html();
            //now loop through the input fields
            $(this).find("input,textarea").each(function () {
                if ($(this).parents('.hidden_form_row').length != 0) {
                    return;
                }
                if (($(this).is('input[name]') || $(this).is('textarea[name]')) && !$(this).is('input[type="hidden"]')) {


                    if ($(this).hasClass('date') || $(this).parent().hasClass('date')) {
                        if ($(this).parent().hasClass('date')) {
                            $(this).datepicker({format: $(this).parent().attr('data-date-format')});
                        } else {
                            $(this).datepicker({format: $(this).attr('data-date-format')});
                        }

                    } else {

                        addTypeaheadMultipleRemote(this, options_url, options_template);
                    }

                }
            });
        });


    });
})(jQuery)
