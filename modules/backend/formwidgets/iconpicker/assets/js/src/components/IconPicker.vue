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
            <div class="form-buttons normalized aim-modal--footer">
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
.vue3-icon-picker {
    cursor: pointer;
}

.input-group i {
    min-width: 14px;
    display: block;
}

button.select-icon {
    padding: 20px;
    border-radius: 5px;
    background-color: #70b2dc;
    font-size: 22px;
    cursor: pointer;
}

.icon-picker-wrap ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: inline-flex;
}

.icon-picker-wrap ul li {
    border: 2px solid #ddd;
    line-height: 1;
    font-size: 20px;
    cursor: pointer;
    padding: 5px;
}

.icon-picker-wrap ul li i {
    font-size: 68px;
    line-height: 1;
    margin: 0;
}

.icon-picker-wrap ul li:nth-child(2) {
    border-left: 0;
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
}

.aim-modal .aim-modal--content {
    position: absolute;
    border-radius: 3px;
    box-shadow: 2px 8px 23px 3px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    font-family: Roboto, Arial, Helvetica, Verdana, sans-serif;
    background-color: #f1f3f5;
    width: 100%;
    margin: auto;
    left: 0;
    right: 0;
}


/* Header */
.aim-modal .aim-modal--header {
    padding: 15px 15px;
    background-color: #fff;
    box-shadow: 0 0 8px rgb(0 0 0 / 10%);
    position: relative;
    z-index: 1;
    font-size: 15px;
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
    height: 700px;
    display: flex;
    min-height: 50px;
    max-height: 85vh;
    overflow: auto;
}


/* Sidebar Tabs */
.aim-modal--sidebar {
    -ms-flex-negative: 0;
    flex-shrink: 0;
    width: 25%;
    background-color: hsla(0, 0%, 100%, .3);
    display: flex;
    flex-flow: column;
}

.aim-modal--sidebar-tabs {
    margin-top: 30px;
}

.aim-modal--sidebar-tab-item {
    padding: 15px 0 15px 45px;
    font-size: 14px;
    color: #6d7882;
    text-align: left;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    text-transform: capitalize;
}

.aim-modal--sidebar-tab-item i {
    font-size: 20px;
    padding-right: 15px;
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
    width: 5px;
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
    padding: 30px 20px 0;
    width: 75%;
}

.aim-modal--icon-preview-inner {
    overflow: auto;
    margin: 25px -15px 0;
    padding: 0 15px 15px;
}

.aim-modal--icon-preview {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    grid-gap: 10px;
    margin: 10px 0;
}


/* search filter */
.aim-modal--icon-search {
    position: relative;
}

.aim-modal--icon-search input {
    width: 100%;
    padding: 8px 15px;
    background-color: #fff;
    border: none;
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
    padding: 10px;
    background-color: #fff;
    -webkit-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05);
    -webkit-border-radius: 3px;
    border-radius: 3px;
    cursor: pointer;
    -webkit-transition: all 0.3s;
    -o-transition: all 0.3s;
    transition: all 0.3s;
    overflow: hidden;
}

.aim-icon-item:hover {
    -webkit-box-shadow: 0 1px 14px rgba(0, 0, 0, 0.16);
    box-shadow: 0 1px 14px rgba(0, 0, 0, 0.16);
}

.aim-icon-item.aesthetic-selected {
    -webkit-box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05), 0 0 0 3px #4ea5e0;
    box-shadow: 0 1px 12px rgba(0, 0, 0, 0.05), 0 0 0 3px #4ea5e0;
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
    font-size: 30px;
    color: #95a5a6;
    padding: 20px;
}

.aim-icon-item-name {
    color: #666;
    font-size: 13px;
    padding-top: 15px;
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
    border-top: 1px solid #e6e9ec;
    text-align: center;
    background-color: #fff;
    border: none;
    display: none;
    justify-content: flex-end;
    padding: 20px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
}

.aim-modal .aim-modal--footer .aesthetic-button {
    height: 40px;
    margin-left: 5px;
}

.aim-modal .aim-modal--footer .aesthetic-button-success {
    padding: 12px 36px;
    color: #fff;
    width: initial;
    font-size: 15px;
}

.aim-modal .aim-modal--footer .aesthetic-button-success:hover {
    background-color: #39b54a;
}

/* preview sidebar */
.aim-sidebar-preview {
    margin-top: auto;
}

.aim-sidebar-preview .aim-icon-item-inner i {
    padding-top: 10px;
    font-size: 110px;
}

.aim-sidebar-preview .aim-icon-item-name {
    padding: 0 0 15px;
    font-size: 16px;
    color: #666;
}

/* Responsive css */

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
}

@media (max-width: 1024px) {
    .aim-modal--icon-preview {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 767px) {
    .aim-sidebar-preview .aim-icon-item-inner i {
        font-size: 70px;
    }
    .aim-modal--icon-preview {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 479px) {
    .aim-modal--sidebar {
        display: none;
    }
}

@media (max-width: 1439px) {
    .aim-modal--sidebar-tab-item {
        padding: 15px 15px 15px 25px;
        font-size: 11px;
    }

    .aim-modal--sidebar-tab-item i {
        font-size: 15px;
    }
}

@media (max-width: 1024px) {
    .aim-modal--sidebar-tab-item i {
        display: none;
    }
}
</style>
