<template>
    <div class="input-group">
        <span class="input-group-addon" @click="togglePicker" style="cursor: pointer">
            <i :class="modelValue"></i>
        </span>
        <input type="text" class="form-control" v-model="modelValue" :name="name" @click="togglePicker">
    </div>

    <div class="aim-modal aim-open" v-if="isVisible">
        <div class="aim-modal--content">
            <div class="aim-modal--header">
                <div class="aim-modal--header-logo-area">
                    <span class="aim-modal--header-logo-title">
                        {{ label }}
                    </span>
                </div>
            </div>
            <div class="aim-modal--body">
                <div class="aim-modal--sidebar">
                    <div class="aim-modal--sidebar-tabs">
                        <div
                            class="aim-modal--sidebar-tab-item"
                            data-library-id="all"
                            v-for="tab in tabs"
                            :key="tab.id"
                            :class="{ 'aesthetic-active': isActiveTab(tab.id) }"
                            @click="setActiveTab(tab)"
                        >
                            <i :class="tab.icon"></i>
                            <span>{{ tab.title }}</span>
                        </div>
                    </div>
                    <div class="aim-sidebar-preview">
                        <div class="aim-icon-item ">
                            <div class="aim-icon-item-inner">
                                <i :class="activeGlyph"></i>
                                <div class="aim-icon-item-name">
                                    {{ getGlyphName(activeGlyph) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="aim-modal--icon-preview-wrap">
                    <div class="aim-modal--icon-search">
                        <input v-model="filterText" placeholder="Filter by name...">
                        <i class="icon-search"> </i>
                    </div>
                    <div class="aim-modal--icon-preview-inner">
                        <div class="aim-modal--icon-preview">
                            <div
                                class="aim-icon-item"
                                v-for="glyph in glyphs"
                                :key="glyph"
                                :class="{ 'aesthetic-selected': isActiveGlyph(glyph) }"
                                @click="setActiveGlyph(glyph)"
                            >
                                <div class="aim-icon-item-inner">
                                    <i :class="glyph"></i>
                                    <div class="aim-icon-item-name">
                                        {{ getGlyphName(glyph) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-buttons aim-modal--footer">
                <button class="btn btn-primary aim-insert-icon-button" @click="insert">Insert</button>
                <button class="btn btn-secondary no-margin-right" @click="closePicker">Close</button>
            </div>
        </div>
    </div>
</template>

<script>
// Forked from https://github.com/hasinhayder/vue3-icon-picker
export default {
    inheritAttrs: false,
    props: ['label', 'propValue', 'name', 'fontLibraries'],
    data() {
        let defaultIcon = this.propValue;
        let activeGlyph = defaultIcon ?? '';
        const tabs = [
            {
                id: 'all',
                title: 'All Icons',
                icon: 'fas icon-star-of-life',
                link: 'all',
            },
        ];
        const prefixes = [];
        const allGlyphs = [];

        this.fontLibraries.forEach((library) => {
            tabs.push({
                id: library.id,
                title: library.title,
                icon: library.listicon,
                link: library,
            });
            prefixes.push(library.prefix);
            allGlyphs.push(...library.icons);
        });

        if (allGlyphs.indexOf(defaultIcon) === -1) {
            activeGlyph = allGlyphs[0] ?? '';
        }

        return {
            modelValue: defaultIcon,
            activeGlyph,
            filterText: '',
            isVisible: false,
            activeTab: tabs[0],
            prefixes,
            allGlyphs,
            tabs,
        };
    },
    computed: {
        glyphs: {
            get() {
                let glyphIcons = [];
                if (this.activeTab.id !== 'all') {
                    glyphIcons = this.activeTab.link.icons;
                } else {
                    glyphIcons = this.allGlyphs;
                }

                if (this.filterText !== '') {
                    const filteredText = this.filterText.toLowerCase();
                    glyphIcons = glyphIcons.filter(
                        // Uncomment this to search excluding the prefix e.g `far icon-`
                        // (item) => item.replaceAll(new RegExp(`^(${prefixes.join('|')})`, 'g'), '').includes(filteredText),
                        (item) => item.includes(filteredText),
                    );
                }
                return glyphIcons;
            },
        },
    },
    methods: {
        setActiveGlyph(glyph) {
            this.activeGlyph = glyph;
        },
        isActiveGlyph(glyph) {
            return this.activeGlyph === glyph;
        },
        isActiveTab(tab) {
            return tab === this.activeTab.id;
        },
        setActiveTab(tab) {
            this.activeTab = tab;
        },
        getGlyphName: (glyph) => glyph.replace(/f.. icon-/g, '').replaceAll('-', ' '),
        insert() {
            this.modelValue = this.activeGlyph;
            this.isVisible = false;
        },
        togglePicker() {
            this.isVisible = !this.isVisible;
        },
        closePicker() {
            this.isVisible = false;
        },
    },
};
</script>

<style scoped>
@import '../css/style.css';
</style>
