<template>
    <div>
        <div class="feed" ref="feed">
            <ul v-if="contact">
                <li v-for="message in messages" :class="`message${(message.to == contact.id) || ( (message.to_group == contact.id) && (message.from == sender.id)) ? ' sent': ' received'}`" :key="message.id">
                    <div v-if="message.text" class="text">
                        {{ message.text }}
                    </div>

                    <div v-if="message.file">
                        <div v-if="message.extension === 'png'||message.extension === 'jpg'||message.extension === 'jpeg'" class="image-container">
                        <img :src="'storage/'+message.file" alt="">
                        </div>
                        <button class="download_button"  v-else @click="downloadWithAxios(message)"><v-icon>attach_file</v-icon>{{message.file}}</button>
                    </div>
                </li>
            </ul>
            <div class="floating-div">
                    <picker v-if="emoStatus" set="emojione" @select="onInput" title="Pick Emoji" />
            </div>
        </div>
        <!-- composer starts -->
        
        <div class="composer">
            <!-- @keydown.enter="send" -->
            <div class="buttons_padding">
                <v-btn @click="toggleEmo" fab small>
                    <v-icon>insert_emoticon </v-icon>
                </v-btn>
                <input ref="fileinput" style="display: none" type="file" @change="onFileSelected">
                <v-btn fab small @click="$refs.fileinput.click()"><v-icon>attach_file</v-icon><span v-if="selectedFile">{{1}}</span></v-btn>
            </div>
            <div class="form__group field">
                <!-- name="message" id='message' -->
                <input type="input" class="form__field" placeholder="Enter Message..." v-model="message" required />
                <label for="name" class="form__label">Message</label>
            </div>
            <!-- <textarea v-model="message" placeholder="Enter Message..."></textarea> -->
            <div class="buttons_padding">
                <button @click="send">send</button>
            </div>
        </div>
    </div>
</template>

<script>
    import {Picker} from 'emoji-mart-vue'

    export default {
        components: { Picker },
        data() {
            return {
                message: '',
                selectedFile: null,
                emoStatus: false
            };
        },
        props: {
            contact: {
                type: Object
            },
            contacttype: '',
            messages: {
                type: Array,
                required: true
            },
            sender: {
                type: Object,
                required: true
            }
        },
        methods: {
            forceFileDownload(response,msg){
                const url = window.URL.createObjectURL(new Blob([response.data]))
                const link = document.createElement('a')
                link.href = url
                link.setAttribute('download', msg.file) //or any other extension
                document.body.appendChild(link)
                link.click()
            },
            downloadWithAxios(mssg){
                axios({
                    method: 'get',
                    url: 'storage/'+mssg.file,
                    responseType: 'arraybuffer'
                })
                .then(response => {
                    this.forceFileDownload(response,mssg) 
                })
                .catch(() => console.log('error occured'))
            },
            scrollToBottom() {
                setTimeout(() => {
                    this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
                },15);
                
            },
            toggleEmo() {
                this.emoStatus = !this.emoStatus;
            },
            onInput(e) {
                if(!e){
                    return false;
                }
                this.message=this.message + e.native;
            },
            onFileSelected(event) {
                this.selectedFile = event.target.files[0]
            },
            send(e) {
                e.preventDefault();
                if (this.message == '' && this.selectedFile == null ) {
                    return;
                }

                if (!this.contact) {
                    return;
                }
                
                const fd = new FormData();
                fd.append('contact_id', this.contact.id);
                fd.append('contact_type', this.contacttype);
                if(this.message) {
                    fd.append('text', this.message);
                }
                if (this.selectedFile) {
                    fd.append('file',this.selectedFile, this.selectedFile.name);    
                }
                
                axios.post('/conversation/send', fd)
                .then((response) => {
                    this.$emit('send', response.data);
                })
                // this.$emit('send', this.message,this.selectedFile);
                this.message = '';
                this.selectedFile = null;
            }
        },
        watch: {
            contact(contact) {
                this.scrollToBottom();
            },
            messages(messages) {
                this.scrollToBottom();
            }
        }
    }
</script>

<style lang="scss" scoped>
.feed {
    background: #f0f0f0;
    height: 530px;
    max-height: 530px;
    overflow-x: hidden;
    overflow-y: scroll;
    border-bottom: 2px solid grey;
    ul {
        list-style-type: none;
        padding: 5px;
        li {
            img {
                border-style: solid blue;
                max-height: 40%;
                max-width: 50%;
            }
            .download_button {
                padding-top: 5px;
                padding-right: 6px;
                padding-bottom: 2px;
                padding-left: 2px;
                border-radius: 30px;
                width: 200px;
                overflow: hidden;
                height: 35px;
                border: 2px solid deepskyblue;
                background-color: aliceblue;
                display: inline-flex;
            }

            .download_button:hover {
                background-color: #2196f3;
                color: white;
            }
            &.message {
                margin: 10px 0;
                width: 100%;
                .text {
                    max-width: 200px;
                    border-radius: 5px;
                    padding: 12px;
                    display: inline-block;
                }
                &.received {
                    text-align: left;
                    .text {
                        background: #b2b2b2;
                    }
                }
                &.sent {
                    text-align: right;
                    .text {
                        background: #81c4f9;
                    }
                }
            }
        }
    }
}
.composer{
    display: flex;
    flex-direction: row;
}
.composer .buttons_padding {
  padding-left: 5px;
  padding-top: 25px;
  padding-right: 5px;
  display: flex;
}
.composer .form__group {
  position: relative;
  //margin-top: 10px;
  padding-left: 15px;
  padding-top: 15px;
  padding-right: 15px;
  width: 85%;
}
.composer .form__field {
  font-family: inherit;
  width: 100%;
  border: 0;
  border-bottom: 2px solid #9b9b9b;
  outline: 0;
  font-size: 1.3rem;
  color: grey;
  padding: 7px 0;
  background: transparent;
  transition: border-color 0.2s;

  &::placeholder {
    color: transparent;
  }

  &:placeholder-shown ~ .form__label {
    font-size: 1.3rem;
    cursor: text;
    top: 20px;
  }
}

.composer .form__label {
  position: absolute;
  top: 0;
  display: block;
  transition: 0.2s;
  font-size: 1rem;
  color: #9b9b9b;
}

.composer .form__field:focus {
  ~ .form__label {
    position: absolute;
    top: 0;
    display: block;
    transition: 0.2s;
    font-size: 1rem;
    color: #1a1d1d;
    font-weight:700;    
  }
  padding-bottom: 6px;  
  font-weight: 700;
  border-width: 3px;
  border-image: linear-gradient(to right, #1a1d1d,#232423);
  border-image-slice: 1;
}
.composer floating-div{
    position: inherit;
}
</style>