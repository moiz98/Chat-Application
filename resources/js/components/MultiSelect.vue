<template>
    <div class="multi-select">
        <form>
            <label>Group Name</label>
            <input type="text" v-model.lazy="group.classname" placeholder="Group Name">
            
            <div style="margin:10% 10%">
                <br>
                <ejs-multiselect id="multiselect" v-model="group.participants" :dataSource='contacts' 
                :fields='localFields' placeholder='Add Participants'
                :itemTemplate='multiselectItemTemplate'
                popupWidth="400px" popupHeight="200px" sortOrder='Ascending'>
                </ejs-multiselect>
            </div>
            <button v-on:click.prevent="postgroup">Create Group</button>
        </form>
    </div>
</template>

<script>
import { MultiSelectPlugin } from '@syncfusion/ej2-vue-dropdowns';
Vue.use(MultiSelectPlugin);

export default {
    props: {
        user: {
            type: Object,
            required: true
        }
    },
    data() {
        // :src="'/uploads/avatars/'
        return {
            multiselectItemTemplate: "<div class='list-wrapper'><span class='${text} e-avatar'></span><span class='text'>${name}</span><span class='text'>${email}</span></div>",
            contacts: [],
            localFields:{},
            group: { classname: '', user: this.user, participants: [] }
        }
    },
    methods: {
        postgroup() {
            axios.post('/group/create', {
                classname: this.group.classname,
                creator: this.user.id,
                participant: this.group.participants
            }).then((data) => {
                // console.log(data.data);
                this.$emit('newgroup', data.data);
                this.group.classname= '';
                this.group.user= this.user;
                this.group.participants= [];
            })
        }
    },
    mounted() {
        axios.get('/group/participant').then((response) => {
            this.contacts = response.data;
            this.localFields = {value: 'id', text: 'profile_image', text: 'name', text: 'email'}
        });
    },
}
</script>
<style>
.vue {
    background-image: url('https://ej2.syncfusion.com/products/images/list-box/vue.svg');
}

@import url(https://cdn.syncfusion.com/ej2/material.css);
.list-wrapper {
    height: inherit;
    position: relative;
    padding: 14px 12px 14px 78px;
}

.list-wrapper .text{
    display: block;
    margin: 0;
    padding-bottom: 3px;
    white-space: normal;
    font-size: 13px;
}

.list-wrapper .e-avatar{
    position: absolute;
    left: 5px;
    border-radius: 50%;
    background-color: whitesmoke;
    font-size: 22px;
    top: calc(50% - 33px);
}
</style>

