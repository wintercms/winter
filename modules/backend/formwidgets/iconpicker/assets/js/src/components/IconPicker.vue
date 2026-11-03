<template>
    <div class="input-group">
        <span class="input-group-addon" @click="togglePicker" style="cursor: pointer">
            <i :class="modelValue"></i>
        </span>
        <input ref="valueInput" type="text" class="form-control" v-model="modelValue" :name="name" @click="togglePicker">
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
                            :data-library-id="tab.id"
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
            <!-- Deliberately NOT `.form-buttons`: that class makes this footer match a
                 backend skin's form action-bar styling (e.g. TailwindUI's fancy layout
                 renders `.form-buttons .btn-primary` as a transparent ghost button with a
                 high-specificity !important rule), which turns Insert white-on-white. The
                 footer is styled on its own `.aim-modal--footer` below instead. -->
            <div class="aim-modal--footer">
                <button type="button" class="btn btn-primary aim-insert-icon-button" @click="insert">Insert</button>
                <button type="button" class="btn btn-secondary no-margin-right" @click="closePicker">Close</button>
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
        getGlyphName: (glyph) => glyph.replace(/(f.. )?icon-/g, '').replaceAll('-', ' '),
        insert() {
            this.modelValue = this.activeGlyph;
            this.isVisible = false;
            // v-model updates the input silently; emit a native change so form widgets and
            // any listeners (dirty-tracking, live preview, etc.) pick up the new value.
            this.$nextTick(() => {
                this.$refs.valueInput?.dispatchEvent(new Event('change', { bubbles: true }));
            });
        },
        togglePicker() {
            this.isVisible = !this.isVisible;
        },
        closePicker() {
            this.isVisible = false;
        },
    },
    watch: {
        // Freeze the page scroll behind the full-screen picker while it's open.
        isVisible(visible) {
            const overflow = visible ? 'hidden' : '';
            document.documentElement.style.overflow = overflow;
            document.body.style.overflow = overflow;
        },
    },
    unmounted() {
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
    },
};
</script>

<style scoped>
.input-group i {
    min-width: 14px;
    display: block;
}

.aim-close {
    opacity: 0;
    visibility: hidden;
    transition: all 0.4s ease-in-out;
}

.aim-open {
    opacity: 1;
    visibility: visible;
    transition: all 0.4s ease-in-out;
}

.aim-modal {
    position: fixed;
    height: 100%;
    width: 100%;
    bottom: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.2);
    z-index: 9999;
    user-select: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.aim-modal .aim-modal--content {
    position: relative;
    border-radius: 3px;
    box-shadow: 2px 8px 23px 3px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    font-family: Roboto, Arial, Helvetica, Verdana, sans-serif;
    background-color: #f1f3f5;
    width: 100%;
    margin: auto;
}


/* Header */
.aim-modal .aim-modal--header {
    padding: 10px 16px;
    background-color: #fff;
    box-shadow: 0 0 8px rgb(0 0 0 / 10%);
    position: relative;
    z-index: 1;
    font-size: 14px;
    color: #405261;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* body */
.aim-modal--body {
    font-size: 12px;
    line-height: 1.5;
    box-sizing: border-box;
    padding: 0;
    height: 640px;
    display: flex;
    min-height: 50px;
    max-height: 82vh;
    overflow: auto;
}


/* Sidebar Tabs */
.aim-modal--sidebar {
    -ms-flex-negative: 0;
    flex-shrink: 0;
    width: 180px;
    background-color: hsla(0, 0%, 100%, .3);
    display: flex;
    flex-flow: column;
}

.aim-modal--sidebar-tabs {
    margin-top: 8px;
}

.aim-modal--sidebar-tab-item {
    padding: 9px 12px 9px 16px;
    font-size: 13px;
    color: #6d7882;
    text-align: left;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
}

.aim-modal--sidebar-tab-item i {
    font-size: 15px;
    padding-right: 8px;
    color: #a4afb7;
}

.aim-modal--sidebar-tab-item.aesthetic-active {
    background-color: #fff;
    -webkit-box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.1);
    box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.1);
}

.aim-modal--sidebar-tab-item.aesthetic-active:after {
    content: "";
    position: absolute;
    height: 100%;
    width: 3px;
    top: 0;
    left: 0;
    background-color: #4ea5e0;
}

.aim-modal--sidebar-tab-item.aesthetic-active i {
    color: #4ea5e0;
}


/* Preview wrapper */
.aim-modal--icon-preview-wrap {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    padding: 12px 16px 0;
    flex: 1;
    min-width: 0;
}

.aim-modal--icon-preview-inner {
    overflow: auto;
    margin: 10px -16px 0;
    padding: 0 16px 12px;
}

.aim-modal--icon-preview {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    grid-gap: 6px;
    margin: 0;
}


/* search filter */
.aim-modal--icon-search {
    position: relative;
}

.aim-modal--icon-search input {
    width: 100%;
    padding: 6px 12px;
    background-color: #fff;
    border: none;
    border-radius: 3px;
    font-size: 13px;
}

.aim-modal--icon-search input::-webkit-input-placeholder {
    font-style: italic;
}

.aim-modal--icon-search input::-moz-placeholder {
    font-style: italic;
}

.aim-modal--icon-search input::-ms-input-placeholder {
    font-style: italic;
}

.aim-modal--icon-search input::placeholder {
    font-style: italic;
}

.aim-modal--icon-search i {
    position: absolute;
    top: 50%;
    -webkit-transform: translateY(-50%);
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    right: 15px;
}

/* Icon Item */
.aim-icon-item {
    position: relative;
    padding: 6px 4px;
    background-color: #fff;
    -webkit-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05);
    -webkit-border-radius: 3px;
    border-radius: 3px;
    cursor: pointer;
    -webkit-transition: box-shadow 0.15s;
    -o-transition: box-shadow 0.15s;
    transition: box-shadow 0.15s;
    overflow: hidden;
}

.aim-icon-item:hover {
    -webkit-box-shadow: 0 1px 14px rgba(0, 0, 0, 0.16);
    box-shadow: 0 1px 14px rgba(0, 0, 0, 0.16);
}

.aim-icon-item.aesthetic-selected {
    -webkit-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05), 0 0 0 2px #4ea5e0;
    box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05), 0 0 0 2px #4ea5e0;
}

.aim-icon-item-inner {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    padding: 1px;
}

.aim-icon-item-inner i {
    font-size: 21px;
    color: #95a5a6;
    padding: 6px 0;
}

.aim-icon-item-name {
    color: #8a959e;
    font-size: 11px;
    padding-top: 2px;
    max-width: 100%;
    white-space: nowrap;
    -o-text-overflow: ellipsis;
    text-overflow: ellipsis;
    overflow: hidden;
    text-transform: capitalize;
    text-align: center;
}

/* Footer */
.aim-modal .aim-modal--footer {
    text-align: center;
    background-color: #fff;
    display: flex;
    justify-content: flex-end;
    padding: 10px 16px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    position: relative;
}

/* Style the footer buttons explicitly rather than leaning on the backend skin's .btn
   styling — some skins (e.g. TailwindUI) leave .btn-primary transparent here, making the
   Insert button white-on-white and invisible. */
.aim-modal .aim-modal--footer .btn {
    padding: 7px 16px;
    border: 1px solid transparent;
    border-radius: 4px;
    font-size: 13px;
    line-height: 1.4;
    cursor: pointer;
}

.aim-modal .aim-modal--footer .btn + .btn {
    margin-left: 8px;
}

.aim-modal .aim-modal--footer .btn-primary {
    background-color: #4ea5e0;
    border-color: #4ea5e0;
    color: #fff;
}

.aim-modal .aim-modal--footer .btn-primary:hover {
    background-color: #3d94cf;
    border-color: #3d94cf;
}

.aim-modal .aim-modal--footer .btn-secondary {
    background-color: #e5e7eb;
    border-color: #e5e7eb;
    color: #374151;
}

.aim-modal .aim-modal--footer .btn-secondary:hover {
    background-color: #d7dbe0;
}

/* preview sidebar (compact swatch chip, pinned to the bottom of the sidebar) */
.aim-sidebar-preview {
    margin-top: auto;
    padding: 10px;
    border-top: 1px solid rgba(0, 0, 0, .06);
}

.aim-sidebar-preview .aim-icon-item-inner {
    flex-direction: row;
    justify-content: flex-start;
    gap: 10px;
    padding: 6px 10px;
}

.aim-sidebar-preview .aim-icon-item-inner i {
    padding: 0;
    font-size: 24px;
}

.aim-sidebar-preview .aim-icon-item-name {
    padding: 0;
    font-size: 12px;
    color: #666;
    text-align: left;
}

/* Responsive css — the auto-fill grid handles column counts, so only the modal width
   caps and the small-screen sidebar collapse remain. */

@media (max-width: 1439px) {
    .aim-modal .aim-modal--content {
        max-width: 990px;
    }
}

@media (min-width: 1440px) {
    .aim-modal .aim-modal--content {
        max-width: 1200px;
    }
}

@media (max-width: 479px) {
    .aim-modal--icon-preview-wrap {
        width: 100%;
    }

    .aim-modal--sidebar {
        display: none;
    }
}
</style>
