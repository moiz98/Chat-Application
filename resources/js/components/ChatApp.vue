<template>
    <div>
        <Navbar :user="user" :contacts="contacts" @new="saveNewcontact" @newgroup="saveNewGroup" />
        <div class="chat-app">
            <Conversation :user="user" :contacttype="selectedContacttype" :contact="selectedContact" :messages="messages" @new="saveNewMessage" @removegroup="removeGroup" @removecontact="removeContact" />
            <ContactsList :groups="groups" :contacts="contacts" @selected="startConversationWith" />
        </div>
    </div>
</template>

<script>
    import Conversation from "./Conversation";
    import ContactsList from "./ContactsList";
    import Navbar from './Navbar';

    export default {
        props: {
            user: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                selectedContact: null,
                selectedContacttype: null,
                messages: [],
                contacts: [],
                groups: []
            };
        },
        mounted() {
            Echo.private(`newgroup.${this.user.id}`)
                .listen('GroupCreated', (e) => {
                    this.groups.push(e.group);
            });

            Echo.private(`messages.${this.user.id}`)
                .listen('NewMessage', (e) => {
                    this.handleIncoming(e.message);
                })
            
            Echo.private(`groupMsg.${this.user.id}`)
                .listen('GroupMessage', (e) => {
                    this.handleIncoming(e.message);
                })
            axios.get(`/group/GroupforUser/${this.user.id}`)
                .then((response) => {
                    this.groups = response.data;
                    // console.log(response.data);
                });

            axios.get('/contacts')
                .then((response) => {
                    this.contacts = response.data;
                    // console.log(response.data);
                });
        },
        methods: {
            startConversationWith(contact,contacttype) {
                
                if (contacttype == 'contact') {
                    this.updateUnreadCount(contact, true, contacttype);
                    axios.get(`/conversation/${contact.id}`)
                        .then((response) => {
                            this.messages = response.data;
                            this.selectedContact = contact;
                            this.selectedContacttype = 'contact'
                        })    
                } else {
                    this.updateUnreadCount(contact.id, true, contacttype);
                    axios.get(`/Gonversation/${contact.id}`)
                        .then((response) => {
                            this.messages = response.data;
                            this.selectedContact = contact;
                            this.selectedContacttype = 'group'
                        })
                }
                
            }, 
            saveNewMessage(text) {
                this.messages.push(text);
            },
            saveNewcontact(newuser) {
                this.contacts.push(newuser);
            },
            saveNewGroup(newgroup) {
                this.groups.push(newgroup);
            },
            removeGroup(group) {
                let i = this.groups.map(item => item.id).indexOf(group) // find index of your object
                this.groups.splice(i, 1) // remove it from array
                this.selectedContact = null;
                this.selectedContacttype = null;
            },
            removeContact(contact) {
                let i = this.contacts.map(item => item.id).indexOf(contact) // find index of your object
                this.contacts.splice(i, 1) // remove it from array
                this.selectedContact = null;
                this.selectedContacttype = null;
            },
            handleIncoming(message) {
                if (this.selectedContact && this.selectedContacttype == 'contact' && message.from == this.selectedContact.id) {
                    this.saveNewMessage(message);
                    return;
                }
                if (this.selectedContact && this.selectedContacttype == 'group' && message.to_group == this.selectedContact.id) {
                    this.saveNewMessage(message);
                    return;
                }

                if (message.to) {
                    this.updateUnreadCount(message.from_contact, false, 'contact');    
                }
                    
                if (message.to_group) {
                    this.updateUnreadCount(message.to_group, false, 'group');    
                }
                
                
            },
            updateUnreadCount(contact, reset, ctype ) {
                if (ctype == 'contact') {
                    this.contacts = this.contacts.map((single) => {
                        if (single.id !== contact.id) {
                            return single;
                        }
                        if (reset)
                            single.unread = 0;
                        else
                            single.unread += 1;
                        return single;
                    })    
                }
                if (ctype == 'group') {
                    this.groups = this.groups.map((single) => {
                        if (single.id !== contact) {
                            return single;
                        }
                        if (reset)
                            single.unread = 0;
                        else
                            single.unread += 1;
                        return single;
                    })
                }
                
            }
        },
        components: {Conversation, ContactsList, Navbar}
    }
</script>

<style lang="scss" scoped>
.chat-app {
    display: flex;
    flex-direction: row-reverse;
    background-color: transparent;
    height: 100%;
}
</style>