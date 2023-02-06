<template>
    <div class="tutorials mt-5">
        <form method="get" :action="actionUrl">
            <div class="row d-flex gx-5">
                <div class="col-md-6">
                    <slot name="content"></slot>
                </div>
                <div class="col-md-6 align-self-end">
                    <div class="hstack gap-3">
                        <input ref="autocomplete" name="search" :placeholder="placeholder" style="width: 100%"/>
                        <button class="btn btn-secondary" type="submit"><i class="fas fa-search"></i></button>
                    </div>

                    <div class="mt-2 small">
                        <div v-for="(audience, i) in audiences" class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" :id="'audience' + i" name="audience" v-model="selectedAudience" :value="audience.key">
                            <label class="form-check-label" :for="'audience' + i">{{audience.label}}</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */
/* globals TomSelect */
export default {
    data() {
        return {
            audiences: [
                {key: 'all', label: 'All User Types'},
                {key: 'editors', label: 'Editors'},
                {key: 'designers', label: 'Designers'},
                {key: 'developers', label: 'Developers'}
            ],
            selectedAudience: 'all'
        }
    },
    props: {
        query: {
            type: String,
            required: false
        },
        audience: {
            type: String,
            required: false
        },
        actionUrl: {
            type: String,
            required: true
        },
        questionsDataSource: {
            type: String,
            required: true
        },
        placeholder: {
            type: String,
            required: true
        }
    },
    mounted() {
        this.selectedAudience = this.audience
        var my = this
        my.select = new TomSelect(this.$refs.autocomplete, {
            maxOptions: null,
            maxItems: 1,
            searchField: 'text',
            labelField: 'text',
            valueField: 'id',
            load: function (query, callback) {
                var formData = new FormData()
                formData.append('q', query)
                fetch(my.questionsDataSource, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(json => {
                        callback(json)
                    }).catch(() => {
                    callback()
                })
            },
            render: {
                option: function (item, escape) {
                    return my.renderOption(item)
                },
                item: function (item, escape) {
                    return `<div>
                                ${item.text}
                            </div>`
                }
            }
        })
        my.select.on('change', function (value) {
            my.$emit('change', value)
        })

        if (this.query) {
            my.select.addOption(this.query)
            my.select.addItem(this.query.id)
        }
    },
    methods: {
        renderOption: function (result) {
            var label
            switch (result.type) {
                case 'question':
                    label = '<span class="ms-auto badge small text-bg-primary">Question</span>';
                    break;
                case 'tag':
                    label = '<span class="ms-auto badge small text-bg-info">Tag</span>';
                    break;
                case 'query':
                    label = '<span class="ms-auto badge small text-bg-secondary">Query</span>';
                    break;
            }


            var option = '<div class="d-flex align-items-center">'
            option += '<div class="tutorial-label">' + result.text + '</div>'
            option += label
            option += '</div>'
            return option
        }
    }
}
</script>