<template>
    <div class="mx-auto max-w-7xl p-4 lg:p-6">
        <form class="grid gap-6 lg:grid-cols-2" @submit.prevent="submitForm">
            <Card
                v-for="(fields, collectionHandle) in collectionFields"
                :key="collectionHandle"
                class="space-y-4 p-5 lg:p-6"
            >
                <div class="space-y-1">
                    <Heading level="3">
                        Queryable Fields for {{ collections[collectionHandle] }}
                    </Heading>

                    <Description>
                        Choose which fields should be searched for Bard link suggestions.
                    </Description>
                </div>

                <div class="space-y-2">
                    <Label :for="`queryable-fields-${collectionHandle}`">
                        Fields
                    </Label>

                    <Combobox
                        :id="`queryable-fields-${collectionHandle}`"
                        v-model="queryableFields[collectionHandle]"
                        :options="collectionFieldOptions[collectionHandle] ?? []"
                        option-label="label"
                        option-value="value"
                        placeholder="Select searchable fields"
                        multiple
                        searchable
                    />

                    <Description>
                        You can select multiple fields per collection.
                    </Description>
                </div>
            </Card>

            <Card class="space-y-4 p-5 lg:col-span-2 lg:p-6">
                <div class="space-y-1">
                    <Heading level="3">Searchable Sitemap URLs</Heading>

                    <Description>
                        Add sitemap URLs that should also be queried for link suggestions.
                    </Description>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="(sitemap, sitemapIndex) in sitemapUrls"
                        :key="`sitemap-${sitemapIndex}`"
                        class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_16rem_auto]"
                    >
                        <div class="space-y-2">
                            <Label :for="`sitemap-url-${sitemapIndex}`">Sitemap URL</Label>

                            <Input
                                :id="`sitemap-url-${sitemapIndex}`"
                                v-model="sitemap.url"
                                input-class="w-full"
                                placeholder="https://example.com/sitemap.xml"
                                type="url"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label :for="`sitemap-site-${sitemapIndex}`">Site</Label>

                            <Select
                                :id="`sitemap-site-${sitemapIndex}`"
                                v-model="sitemap.site"
                                :options="siteOptions"
                                option-label="label"
                                option-value="value"
                                placeholder="Select a site"
                            />
                        </div>

                        <div class="flex items-end">
                            <Button
                                v-if="sitemapUrls.length > 1"
                                type="button"
                                variant="danger"
                                @click="removeSitemapUrl(sitemapIndex)"
                            >
                                Remove
                            </Button>
                        </div>
                    </div>
                </div>

                <div>
                    <Button type="button" variant="secondary" @click="addSitemapUrl">
                        Add Sitemap URL
                    </Button>
                </div>
            </Card>

            <div class="lg:col-span-2">
                <div class="sticky bottom-4 flex justify-end">
                    <Button type="submit">Save Settings</Button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { Button, Card, Combobox, Description, Heading, Input, Label, Select } from '@statamic/cms/ui';
import axios from 'axios';

const { collections, collectionFields, initialSettings, sites } = defineProps({
    collections: {
        type: Object,
        required: true,
    },
    collectionFields: {
        type: Object,
        required: true,
    },
    initialSettings: {
        type: Object,
        required: true,
    },
    sites: {
        type: Object,
        required: true,
    },
});

const getDefaultSite = () => {
    return Object.keys(sites)[0] ?? null;
};

const initializeQueryableFields = () => {
    const initialQueryableFields = {};

    Object.keys(collectionFields).forEach((collectionHandle) => {
        initialQueryableFields[collectionHandle] = initialSettings[collectionHandle] || [];
    });

    return initialQueryableFields;
};

const getInitialSitemapUrls = () => {
    const configuredSitemapUrls = initialSettings.sitemapUrls;

    if (Array.isArray(configuredSitemapUrls) && configuredSitemapUrls.length > 0) {
        return configuredSitemapUrls.map((sitemap) => ({
            url: sitemap.url ?? '',
            site: sitemap.site ?? getDefaultSite(),
        }));
    }

    return [
        {
            url: '',
            site: getDefaultSite(),
        },
    ];
};

const collectionFieldOptions = computed(() => {
    return Object.fromEntries(
        Object.entries(collectionFields).map(([collectionHandle, fields]) => {
            return [
                collectionHandle,
                Object.entries(fields).map(([fieldHandle, fieldLabel]) => ({
                    label: fieldLabel,
                    value: fieldHandle,
                })),
            ];
        }),
    );
});

const siteOptions = computed(() => {
    return Object.entries(sites).map(([siteHandle, siteConfiguration]) => ({
        label: siteConfiguration.name,
        value: siteHandle,
    }));
});

const queryableFields = reactive(initializeQueryableFields());
const sitemapUrls = ref(getInitialSitemapUrls());

const getSelectedFieldsOnly = () => {
    const selectedFields = {};

    Object.keys(queryableFields).forEach((collectionHandle) => {
        const selectedFieldsForCollection = queryableFields[collectionHandle];

        if (selectedFieldsForCollection && selectedFieldsForCollection.length > 0) {
            selectedFields[collectionHandle] = selectedFieldsForCollection;
        }
    });

    return selectedFields;
};

const addSitemapUrl = () => {
    sitemapUrls.value.push({
        url: '',
        site: getDefaultSite(),
    });
};

const removeSitemapUrl = (sitemapIndex) => {
    sitemapUrls.value.splice(sitemapIndex, 1);
};

const submitForm = async () => {
    const payload = {
        queryable_collections: Object.keys(queryableFields),
        queryable_fields: getSelectedFieldsOnly(),
        sitemap_urls: sitemapUrls.value.filter((sitemap) => {
            return Boolean(sitemap.url && sitemap.site);
        }),
    };


    try {
        const response = await axios.post('/cp/suggestions/settings', payload);        

        if (response.data.success) {
            Statamic.$toast.success('Settings saved successfully');

            return;
        }

        Statamic.$toast.error('Something went wrong');
    } catch {
        Statamic.$toast.error('Something went wrong');
    }
};
</script>
