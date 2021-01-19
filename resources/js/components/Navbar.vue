<template>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">            
            <a class="navbar-brand" @click="toHome">
                ChatApp
            </a>
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                    <!-- Button trigger modal for create group -->
                    <button style="color:black;background-color:transparent;border-color:transparent;" type="button" class="btn btn-primary" data-toggle="modal" data-target="#createGroupForm">
                        Create Group
                    </button>
                    
                    <!-- Modal -->
                    <div class="modal fade" id="createGroupForm" tabindex="-1" role="dialog" aria-labelledby="createGroupFormCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="createGroupFormLongTitle">Create Group</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <MultiSelect :user="user" @newgroup="AddGroup" ></MultiSelect>
                            </div>
                        </div>
                        </div>
                    </div>
                    <!-- Button trigger modal for add user -->
                    <button style="color:black;background-color:transparent;border-color:transparent;" type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddUserForm">
                        Add Contact
                    </button>
                    
                    <!-- Modal -->
                    <div class="modal fade" id="AddUserForm" tabindex="-1" role="dialog" aria-labelledby="AddUserFormCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="AddUserFormLongTitle">Add Contact</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form>
                                    <label>Email  </label>
                                    <input type="email" v-model.lazy="adduser.email" placeholder="Enter Friend's Email..." style="width:300px"><br>
                                    <button id="closeadduser" v-on:click.prevent="AddContact">Add</button>
                                </form>
                            </div>
                        </div>
                        </div>
                    </div>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    <li class="nav-item dropdown" style="display:flex;align-items: center;">
                        <img :src="'storage/avatars/'+user.profile_image" alt="" style="width: 32px; height:32px; position:absolute; left: 10px; border-radius:50%;">
                        <div style="padding-left:50px;">{{user.name}}</div>
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre style="position: relative;">
                            <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <span></span>
                            <a class="dropdown-item" data-toggle="modal" data-target="#UserProfileForm">
                                Profile
                            </a>
                            <a class="dropdown-item" @click="toLogout">
                                Logout
                            </a>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="UserProfileForm" tabindex="-1" role="dialog" aria-labelledby="UserProfileFormCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="UserProfileFormLongTitle">User Profile</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <!-- <h5>this is where user profile stuff goes</h5> -->
                                    <div class="avatar">
                                        <img :src="'storage/avatars/' + user.profile_image">
                                    </div>
                                    
                                    <div class="profileUpdate">
                                        <input ref="fileinput" style="display: none" type="file" @change="onFileSelected">
                                        <v-btn id="PPButton" @click="$refs.fileinput.click()">Profile Picture</v-btn>
                                        <v-btn id="PPSubmit" style="display: none" @click="updateInfo()">Update</v-btn>
                                    </div>
                                    <br>
                                    <div class="nameblock" style="display:flex;">
                                        <h5 style="margin-bottom: 0px;">Name</h5> <button @click="editName()" style="background-color: darkgrey;margin-right: 15px;margin-left: 350px;border-radius: 40px;border: 1px solid black;width:50px;">Edit</button>
                                    </div>
                                    <div v-if="this.edit_name">
                                        <v-text-field v-model="user.name" id="nameeditfield" @blur="showupdatebutton()" @keydown.enter="showupdatebutton()" />
                                    </div>
                                    <div v-else @click="editName()">
                                        <h5>{{user.name}}</h5>
                                    </div>
                                    <br>
                                    <div class="nameblock" style="display:flex;">
                                        <h5 style="margin-bottom: 0px;">Status</h5> <button @click="editDesc()" style="background-color: darkgrey;margin-right: 15px;margin-left: 347px;border-radius: 40px;border: 1px solid black;width:50px">Edit</button>
                                    </div>
                                    <div v-if="this.edit_desc">
                                        <v-text-field v-model="user.status" id="desceditfield" @blur="showupdatebutton()" @keydown.enter="showupdatebutton()" />
                                    </div>
                                    <div v-else @click="editDesc()">
                                        <h5>{{user.status}}</h5>
                                    </div>
                                    <br>
                                </div>
                            </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>  
</template>

<script>
    import MultiSelect from './MultiSelect'; 

    export default {
        props: {
            user:{
                type: Object,
                required: true
            },
            contacts: {
                type: Array,
                default: []
            },
        },
        data() {
            return {
                adduser: { email: '', user: this.user.id},
                edit_name: false,
                edit_desc: false,
                selectedFile: null,
                disableSubmit:true,
            }
        },
        methods: {
            toprofile() {
                window.location.href = '/profile';
            },
            toHome() {
                window.location.href = '/home';
            },
            toLogout() {
                axios.post('/user/logout').then((response)=>{
                    if(response.status == 200){
                        window.location.href = '/';
                    }
                });
            },
            AddContact() {
                axios.post('/addcontact', this.adduser).then((response)=>{
                    if(response.data.status == 1)
                    {
                        this.adduser.email = '';
                        $(function() {
                            $('#closeadduser').click(function() {
                                $('#AddUserForm').modal('hide');
                            });
                        });
                        this.$emit('new', response.data.contact);
                    }
                    if(response.data.status == 0)
                    {
                        this.adduser.email = '';
                        $(function() {
                            $('#closeadduser').click(function() {
                                $('#AddUserForm').modal('hide');
                            });
                        });
                        alert('User Is Already Present In Your Contacts')
                    }
                    if(response.data.status == 2)
                    {
                        this.adduser.email = '';
                        $(function() {
                            $('#closeadduser').click(function() {
                                $('#AddUserForm').modal('hide');
                            });
                        });
                        alert('No User Registered In Our Database with provided Email.')
                    }
                });
            },
            AddGroup(ggroup) {
                this.$emit('newgroup', ggroup);
                $(function() {
                    $('#createGroupForm').modal('hide');
                });
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
                fd.append('user_id', this.user.id);
                fd.append('user_name', this.user.name);
                fd.append('user_status', this.user.status);
                if (this.selectedFile) {
                    fd.append('file',this.selectedFile, this.selectedFile.name);    
                }
                axios.post('/profile', fd)
                .then((response) => {
                    console.log(response);
                    this.edit_name = false;
                    this.edit_desc = false;
                })
            },
            showupdatebutton() {
                document.getElementById('PPSubmit').style.display='inline';
            },
            onFileSelected(event) {
                this.selectedFile = event.target.files[0];
                document.getElementById('PPButton').style.background='yellowgreen';
                document.getElementById('PPSubmit').style.display='inline';
            },
        },
        components: {MultiSelect}
    }
</script>

<style lang="scss" scoped>
.avatar {
    flex: 1;
    display: flex;
    align-items: center;
    padding-bottom: 10px;
    img {
        width: 150px;
        border-radius: 50%;
        margin: 0 auto;
    }
}
</style>