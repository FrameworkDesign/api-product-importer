<template>
    <div class="w-full p-0">
        <div class="flex flex-wrap flex-col">
            <a href="#" @click.prevent="openModal" class="btn">Open</a>
        </div>

        <modal
            v-if="modalOpen"
            name="updater-composer-output"
            :overflow="false"
            width="75%"
        >
            <button
                class="btn-close absolute z-10 top-0 right-0 mt-2 mr-2"
                :aria-label="'Close'"
                @click="modalOpen = false"
                v-html="'&times'"
            />
            <div class="p-3 relative">
                <div class="content mt-0 mb-2">
                    <h2 class="mb-0">Get RST Product Portal Data</h2>
                    <p class="max-w-lg">Run through the steps to pull latest data for this product.</p>
                </div>
                <div class="mt-0">
                    <div class="content mb-2">
                        <h4>Step <span v-text="step"></span></h4>

                        <div v-if="step === 1">
                            <p>
                                1. get latest data from api <br>
                                - get api into local ApiProduct model
                            </p>

                            <div v-if="productDataCollected" class="border-dotted border-2 border-black p-2 mb-2">
                                <h3 class="mb-0">We have the latest data, click <span class="text-blue-300">Next</span> to continue</h3>
                            </div>

                            <button class="btn" @click.prevent="getLatestData">
                                Get Data
                            </button>
                        </div>

                        <div v-if="step === 2">
                            <p>2. Mapping step needed to work out what fields will go where</p>
                            <div style="overflow-y: scroll; max-height: 400px;">
                                <map-collection-fields-panel
                                    name="mapping"
                                    id="mapping"
                                    :savedMapping="savedMapping"
                                    :config="fieldMappingConfig"
                                    @mapping="updateMapping"
                                    @custommapping="updateCustomMapping"
                                ></map-collection-fields-panel>
                            </div>
                        </div>
                        <div v-if="step === 3">
                            <p>3. show what changes will be made to the system</p>

                            <div style="overflow-y: scroll; max-height: 400px;">
                                <h3>Mapping</h3>
                                <div class="flex justify-between mapping-header-row">
                                    <p class="p-.5 mb-0" style="width:150px;"><strong>Field</strong></p>
                                    <p class="p-.5 mb-0 border-l border-grey-60" style="width:45%; padding-right: 1%;"><strong>Current value</strong></p>
                                    <p class="p-.5 mb-0 border-l border-grey-60" style="width:45%;"><strong>New value</strong></p>
                                </div>
                                <template v-for="field in mapping">
                                    <div class="flex justify-between field-row">
                                        <p class="p-.5 mb-0 break-all font-bold" style="width:150px;" v-text="field"></p>
                                        <p class="p-.5 mb-0 border-l border-grey-50" style="width:45%; padding-right: 1%;" v-text="currentProductValues[field]"></p>
                                        <p class="p-.5 mb-0 border-l border-grey-50 break-all" style="width:45%;" v-text="product[field]"></p>
                                    </div>
                                </template>

                                <h3 class="mt-2">Custom Mapping</h3>
                                <div class="flex justify-between mapping-header-row">
                                    <p class="p-.5 mb-0" style="width:150px;"><strong>Field</strong></p>
                                    <p class="p-.5 mb-0 border-l border-grey-60" style="width:45%; padding-right: 1%;"><strong>Current value</strong></p>
                                    <p class="p-.5 mb-0 border-l border-grey-60" style="width:45%;"><strong>New value</strong></p>
                                </div>
                                <template v-for="field in customMapping">
                                    <div class="flex justify-between field-row">
                                        <p class="p-.5 mb-0 break-all font-bold"  style="width:150px;" v-text="field"></p>
                                        <p class="p-.5 mb-0 border-l border-grey-50" style="width:45%; padding-right: 1%;" v-text="currentProductValues[field]"></p>
                                        <p class="p-.5 mb-0 border-l border-grey-50 break-all" style="width:45%;" v-text="product[field]"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div v-if="step === 4">
                            <p>4. commit/discard new changes</p>

                            <button
                                v-if="!finished"
                                class="btn-primary w-auto mb-1 flex justify-center items-center"
                                @click="commitChanges"
                            >
                                Commit Changes
                            </button>
                            <p v-if="pollChecking" class="mb-1">Checking...</p>
                            <button
                                v-if="finished"
                                class="btn-primary w-auto flex justify-center items-center"
                                @click="closeModal"
                            >
                                Finish
                            </button>
                        </div>

                        <div class="mt-2">
                            <div class="api-product-data-loader" v-if="loading"></div>
                        </div>

                        <div class="mt-2 flex justify-between items-center">
                            <button v-if="step > 1" class="btn-primary w-auto mr-auto flex justify-center items-center" @click="prevStep">
                                Prev
                            </button>

                            <button v-if="step < 4 && productDataCollected == true" class="btn w-auto ml-auto flex justify-center items-center" @click="nextStep">
                                Next
                            </button>
                        </div>
                    </div>


                </div>
            </div>
        </modal>
    </div>
</template>

<script>
export default {
    mixins: [ Fieldtype ],

    props: ['config'],
    mounted() {
        // steps system
        // 1. get latest data from api
        //   - get api into local ApiProduct model
        // 2. Mapping step needed to work out what fields will go where
        // 3. show what changes will be made to the system
        // 4. commit/discard new changes
        this.currentProductValues = this.$store.state.publish.base.values
        this.sku = this.$store.state.publish.base.values.sku
    },

    data: function() {
        return {
            modalOpen: false,
            step: 1,
            savedMapping: {},
            loading: false,
            currentProductValues: {},
            product: {},
            productDataCollected: false,
            fieldMappingConfig: {},
            mapping: {},
            customMapping: {},
            finished: false,
            pollChecking: false,
            pollInterval: null
        };
    },

    methods: {
        openModal() {
            this.modalOpen = true
        },
        closeModal() {
            this.modalOpen = false
            this.step = 1
            this.loading = false
            this.savedMapping = {}
            this.currentProductValues = {}
            this.product = {}
            this.productDataCollected = false
            this.fieldMappingConfig = {}
            this.mapping = {}
            this.customMapping = {}
            this.finished = false
            this.pollChecking = false
            this.pollInterval = null
        },
        nextStep() {
            this.step++
        },
        prevStep() {
            this.step--
        },
        updateMapping(mapping) {
            this.mapping = mapping
        },
        updateCustomMapping(customMapping) {
            this.customMapping = customMapping
        },
        commitChanges() {
            this.loading = true
            this.$axios.post(cp_url(`weareframework/api-product-importer/api/store/${this.sku}`), {
                site: this.$store.state.publish.base.site,
                collection: this.$store.state.publish.base.blueprint.handle,
                mapping: this.mapping,
                custom_mapping: this.customMapping,
            }).then(response => {
                this.$toast.success('Success! processing data. We will let you know when its finished')
                this.pollFinished(response.data.data)
                this.pollChecking = true
            })
            .catch((error) => {
                console.log(error);
                this.$toast.error('Something went wrong')
            })
            .finally(() => {
                setTimeout(() => {
                    this.loading = false
                }, 200)
            })
        },
        pollFinished(uuid) {
            this.pollInterval = setInterval(() => {
                this.$axios.get(cp_url(`weareframework/api-product-importer/api/poll/${uuid}`))
                    .then(response => {
                        if(response.data.success == true) {
                            this.$toast.success('Finished. Reloading page with new data')
                            window.location.reload()
                        } else {
                            this.$toast.error('Still running')
                        }
                    }).catch(error => {
                        console.log(error);
                        this.$toast.error('Something has gone wrong. Please try again')
                        this.clearIntervalNow()
                    })
            }, 5000);
        },
        clearIntervalNow() {
            clearInterval(this.pollInterval)
            this.pollChecking = false
        },
        getLatestData() {
            this.loading = true
            this.$axios.get(cp_url(`weareframework/api-product-importer/api/pull/${this.sku}`)).then(response => {

                if (response.data && response.data.product && response.data.success) {
                    this.product = response.data.product
                    this.productDataCollected = true
                    this.fieldMappingConfig = response.data.mapping
                    this.savedMapping = response.data.savedMapping
                }
            })
            .finally(() => {
                setTimeout(() => {
                    this.loading = false
                }, 200)
            })
        }
    }
}
</script>
<style>
.api-product-data-loader,
.api-product-data-loader:after {
    border-radius: 50%;
    width: 4em;
    height: 4em;
}

.api-product-data-loader {
    font-size: 7px;
    position: relative;
    text-indent: -9999em;
    border-top: 0.75em solid rgba(0, 0, 0, 0.2);
    border-right: 0.75em solid currentColor;
    border-bottom: 0.75em solid currentColor;
    border-left: 0.75em solid currentColor;
    transform: translateZ(0);
    animation: api-product-data-loader 1.1s infinite linear;
}

@keyframes api-product-data-loader {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.field-row:nth-child(odd) {
    background-color: #f4f4f4;
}
.mapping-header-row {
    border-top: 1px solid #d3d3d3;
}

.field-row {
    border-bottom: 1px solid #c4ccd4;
}
</style>
