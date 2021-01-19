<template>
    <div class="Conversationblock">
        
        <transition name="slide-fade">
            <div v-if="show_info" class="GPInfo">
                <div v-if="this.contacttype == 'group'">
                    <div class="avatar">
                        <img :src="'storage/avatars/' + contact.profile_image">
                    </div>
                    
                    <div class="profileUpdate">
                        <input ref="fileinput" style="display: none" type="file" @change="onFileSelected">
                        <v-btn id="PPButton" @click="$refs.fileinput.click()">Profile Picture</v-btn>
                        <v-btn id="PPSubmit" style="display: none" @click="updateInfo()">Update</v-btn>
                    </div>
                    <br><br>
                    <div class="nameblock" style="display:flex;">
                        <h5 style="margin-bottom: 0px;">Name</h5> <button @click="editName()" style="background-color: darkgrey;margin-right: 15px;margin-left: 165px;border-radius: 40px;border: 1px solid black;width:50px;">Edit</button>
                    </div>
                    <div v-if="this.edit_name">
                        <v-text-field v-model="contact.name" id="nameeditfield" @blur="updateInfo()" @keydown.enter="updateInfo()" />
                    </div>
                    <div v-else @click="editName()">
                        <h5>{{contact.name}}</h5>
                    </div>
                    <br><br>
                    <div class="nameblock" style="display:flex;">
                        <h5 style="margin-bottom: 0px;">Description</h5> <button @click="editDesc()" style="background-color: darkgrey;margin-right: 15px;margin-left: 125px;border-radius: 40px;border: 1px solid black;width:50px">Edit</button>
                    </div>
                    <div v-if="this.edit_desc">
                        <v-text-field v-model="contact.description" id="desceditfield" @blur="updateInfo()" @keydown.enter="updateInfo()" />
                    </div>
                    <div v-else @click="editDesc()">
                        <h5>{{contact.description}}</h5>
                    </div>
                    <v-btn id="removeGroup" style="bottom: -160px;" @click="removeGroup()">Remove Group</v-btn>
                </div>

                <div v-if="this.contacttype == 'contact'">
                    <!-- <h1>this is contact container</h1> -->
                    <div class="avatar">
                        <img :src="'storage/avatars/' + contact.profile_image">
                    </div> 
                    <br>
                    <div class="nameblock" style="display:flex;">
                        <h5 style="margin-bottom: 0px;">Name</h5>    
                        <h5 style="margin-left: 15px;margin-bottom: 0px;">{{contact.name}}</h5>
                    </div>
                    <br>
                    <div class="nameblock" style="display:flex;">
                        <h5 style="margin-bottom: 0px;">Status</h5>
                        <h5 style="margin-left: 1px;margin-bottom: 0px;">{{contact.status}}</h5>
                    </div>
                    <br>
                    <v-btn id="removeContact" style="bottom: -180px;" @click="removeContact()">Remove Contact</v-btn>
                </div>

            </div>
        </transition>

        <div class="conversation">
            <span v-on:click="show_info = !show_info"><h1>{{ contact ? contact.name : "Select Contact"}}</h1></span>
            <MessagesFeed :contact="contact" :messages="messages" :sender="user" :contacttype="contacttype" @send="sendMessage" />
        </div>
    </div>
</template>

<script>
    import MessagesFeed from "./MessagesFeed";
    // import MessageComposer from "./MessageComposer";

    export default {
        props: {
            contact: {
                type: Object,
                default: null
            },
            messages: {
                type: Array,
                default: []
            },
            contacttype: '',
            user: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                show_info: false,
                edit_name: false,
                edit_desc: false,
                selectedFile: null,
                disableSubmit:true,
            };
        },
        methods: {
            sendMessage(data) {
                this.$emit('new', data);
            },
            editName() {
                this.edit_name = true;
                setTimeout(()=>{
                    document.getElementById('nameeditfield').focus()
                },1)
            },
            editDesc() {
                this.edit_desc = true;
                setTimeout(()=>{
                    document.getElementById('desceditfield').focus()
                },1)
            },
            updateInfo() {
                const fd = new FormData();
                fd.append('contact_id', this.contact.id);
                fd.append('contact_name', this.contact.name);
                fd.append('contact_desc', this.contact.description);
                if (this.selectedFile) {
                    fd.append('file',this.selectedFile, this.selectedFile.name);    
                }
                axios.post('/group/updateInfo', fd)
                .then((response) => {
                    console.log(response);
                    this.edit_name = false;
                    this.edit_desc = false;
                })
            },
            onFileSelected(event) {
                this.selectedFile = event.target.files[0];
                document.getElementById('PPButton').style.background='yellowgreen';
                document.getElementById('PPSubmit').style.display='inline';
            },
            removeContact() {
              const fd = new FormData();
                fd.append('contact_id', this.contact.id);
                fd.append('user_id', this.user.id);
                axios.post('/contacts/remove', fd)
                .then((response) => {
                    console.log(response);
                    if (response.data == 1) {
                        this.$emit('removecontact', this.contact.id);                    
                    }
                })  
            },
            removeGroup() {
                const fd = new FormData();
                fd.append('group_id', this.contact.id);
                fd.append('user_id', this.user.id);
                axios.post('/group/removegroup', fd)
                .then((response) => {
                    console.log(response);
                    if (response.data == 1) {
                        this.$emit('removegroup', this.contact.id);                    
                    }
                })
            },
        },
        components: {MessagesFeed}    
    }
</script>

<style lang="scss" scoped>

.Conversationblock {
    flex: 8;
    justify-content: space-between;
    max-height: 100%;
    height: 660px;
    overflow: hidden;
    background-color: #fff;
    
    // padding: 0 20px;
    margin-top: 5px;
    margin-bottom: 5px;
    margin-left: 10px;
    margin-right: 10px;
    border-top-left-radius: 10px;
    border-top-right-radius: 40px;
    border-bottom-right-radius: 40px;
    border-bottom-left-radius: 10px;
    box-shadow: 0 10px 40px rgba(159, 162, 177, .8) !important;
    display: flex;
    flex-direction: inherit;
}
.GPInfo {
    flex: 3;
    // transition: 0.5s;   
}
.avatar {
    flex: 1;
    display: flex;
    align-items: center;
    padding: 45px 0px;
    img {
        width: 150px;
        border-radius: 50%;
        margin: 0 auto;
    }
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
.conversation {
    flex: 7;
    justify-content: space-between;
    max-height: 100%;
    height: 660px;
    overflow: hidden;
    background-color: #fff;
    left: 0;

    
    border-top-left-radius: 10px;
    border-top-right-radius: 40px;
    border-bottom-right-radius: 40px;
    border-bottom-left-radius: 10px;
    box-shadow: 0 10px 40px rgba(159, 162, 177, .8) !important;

    h1 {
        font-size: 20px;
        padding: 10px;
        margin: 0;
        border-bottom: 3px solid grey;
    }
}
</style>