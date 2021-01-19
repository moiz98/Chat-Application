<template>
    <div class="contacts-list">
        <button class="button_headings" v-on:click="show_class = !show_class">
            Groups
        </button>
        <transition name="slide-fade">
            <div v-if="show_class">
                <ul>
                    <li v-for="group in sortedGroups" :key="group.id" @click="selectContact(group,'group')" :class="{ 'selected': group == selected }">
                        <div class="avatar">
                            <img :src="'storage/avatars/' + group.profile_image" :alt="group.name">
                        </div>
                        <div class="contact">
                            <p class="name">{{ group.name }}</p>
                            <!-- <p class="email">{{ contact.email}}</p> -->
                        </div>
                        <span class="unread" v-if="group.unread">{{ group.unread }}</span>
                    </li>
                </ul>
            </div>
        </transition>
        <button class="button_headings" v-on:click="show_contacts = !show_contacts">
            Contacts
        </button>
        <transition name="slide-fade">
            <div v-if="show_contacts">
                <ul>
                    <li v-for="contact in sortedContacts" :key="contact.id" @click="selectContact(contact,'contact')" :class="{ 'selected': contact == selected }">
                        <div class="avatar">
                            <img :src="'storage/avatars/' + contact.profile_image" :alt="contact.name">
                        </div>
                        <div class="contact">
                            <p class="name">{{ contact.name }}</p>
                            <p class="email">{{ contact.email}}</p>
                        </div>
                        <span class="unread" v-if="contact.unread">{{ contact.unread }}</span>
                    </li>
                </ul>        
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        props: {
            contacts: {
                type: Array,
                default: []
            },
            groups: {
                type: Array,
                default: []
            }
        },
        data() {
            return {
                selected: this.contacts.length ? this.contacts[0] : null,
                show_class: false,
                show_contacts: true
            };
        },
        methods: {
            selectContact(contact,contacttype) {
                this.selected = contact;

                this.$emit('selected', contact,contacttype);
            },
            
        },
        computed: {
            sortedGroups() {
                return _.sortBy(this.groups, [(group) => {
                    if (group == this.selected) {
                        return Infinity;
                    }
                    return group.msg_time;
                }]).reverse();
            },
            sortedContacts() {
                return _.sortBy(this.contacts, [(contact) => {
                    if (contact == this.selected) {
                        return Infinity;
                    }
                    return contact.msg_time;
                }]).reverse();
            }
        }
    }
</script>

<style lang="scss" scoped>
/* width */
::-webkit-scrollbar {
  width: 10px;
}

/* Track */
::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px grey;
  border-radius: 40px;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: darkgrey;
  border-radius: 10px;
}
.contacts-list {
    flex: 2;
    display: flex;
    flex-direction: column;
    max-height: 100%;
    height: 660px;
    overflow-x: hidden;
    overflow-y: auto;
    
    background-color: #fff;
    border-right: 1px solid #a6a6a6;
    padding: 0 20px;
    margin-top: 5px;
    margin-bottom: 5px;
    margin-left: 10px;
    margin-right: 10px;
    // border-radius: 40px;
    border-top-left-radius: 40px;
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
    border-bottom-left-radius: 40px;

    box-shadow: 0 10px 40px rgba(159, 162, 177, .8) !important;
    .button_headings {
        box-shadow: 0 6px 50px rgba(159, 162, 177, 0.8) !important;
        padding: 10px;
        border-radius: inherit;
        margin-top: 15px;
        width: 100%;
        // margin-left: -16px;
    }
    .slide-fade-enter-active {
        transition: all .3s ease;
    }
    .slide-fade-leave-active {
        transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }
    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active below version 2.1.8 */ {
        transform: translateX(10px);
        opacity: 0;
    }
    ul {
        list-style-type: none;
        padding-right: 0;
        
        li {
            display: flex;
            padding: 2px;
            border-bottom: 1px solid #aaaaaa;
            height: 80px;
            position: relative;
            cursor: pointer;
            &.selected {
                background: #dfdfdf;
            }
            span.unread {
                background: #82e0a8;
                color: #fff;
                position: absolute;
                right: 11px;
                top: 20px;
                display: flex;
                font-weight: 700;
                min-width: 20px;
                justify-content: center;
                align-items: center;
                line-height: 20px;
                font-size: 12px;
                padding: 0 4px;
                border-radius: 3px;
            }
            .avatar {
                flex: 1;
                display: flex;
                align-items: center;
                img {
                    width: 35px;
                    border-radius: 50%;
                    margin: 0 auto;
                }
            }
            .contact {
                flex: 3;
                font-size: 10px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                justify-content: center;
                p {
                    margin: 0;
                    &.name {
                        font-weight: bold;
                    }
                }
            }
        }
    }
}
</style>