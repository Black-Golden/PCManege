<template ref="uploadImage">
  <div class="finish_room">
    <div class="finish_room2">
      <div :images="photo">
        <div :key="index" v-for="(item, index) in photo" class="room_img">
          <img v-lazy="typeof item == 'string' ? item : item.url" />
          <div v-if="!disabled" @click="deleteImg(index)" class="im-button">
            <div class="im-close"></div>
            <div class="im-close1"></div>
          </div>
        </div>
      </div>
      <div v-if="photo.length < limit" v-loading="loading" class="room_add_img">
        <span style="margin-top: 35px"><img src="../assets/add_img.png"/></span>
        <input :disabled="disabled" @change="add_img" type="file" />
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  props: {
    limit: {
      type: Number,
      default() {
        return 1
      },
    },
    updDir: {
      type: String,
      default() {
        return 'error'
      },
    },
    disabled: {
      type: Boolean,
      default() {
        return false
      },
    },
    isCompress: {
      type: Boolean,
      default() {
        return false
      },
    },
    value: {
      type: [Array, String],
      default() {
        return []
      },
    },
  },
  data: function() {
    return {
      photo: [],
      loading: false,
    }
  },
  watch: {
    photo() {
      if (this.limit == 1) {
        this.$emit('input', this.photo.length > 0 ? this.photo[0].url : '')
      } else {
        this.$emit('input', this.photo)
      }
    },
    value() {
      if (this.limit == 1) {
        if (this.value instanceof Array) {
          this.photo = []
        } else {
          this.photo = this.value ? [{ url: this.value }] : []
        }
      } else {
        // this.photo = this.value
        // let photos=this.value
        // photos=photos.map(item=>{
        //     if(typeof item=='string'){
        //         return {fileName:"",url:item}
        //     }else{
        //         return item
        //     }
        // })
        this.photo = this.value || []
      }
      if (typeof this.photo == 'string') {
        this.photo = [this.photo]
      }
    },
  },
  mounted() {
    // console.log(this.value)
    // console.log(typeof this.value)
    if (this.limit == 1) {
      if (this.value instanceof Array) {
        this.photo = []
      } else {
        this.photo = this.value ? [this.value] : []
      }
    } else {
      // let photos=this.value
      //     photos=photos.map(item=>{
      //         if(typeof item=='string'){
      //             return {fileName:"",url:item}
      //         }else{
      //             return item
      //         }
      //     })
      //     this.photo=photos
      this.photo = this.value || []
    }
    if (typeof this.photo == 'string') {
      this.photo = [this.photo]
    }
    // console.log(this.photo)
  },
  methods: {
    deleteImg(index) {
      this.photo.splice(index, 1)
    },
    compress(img) {
      var url = null
      var canvas = document.createElement('canvas')
      var scale = img.height / img.width
      canvas.width = 720
      canvas.height = 720 * scale

      var ctx = canvas.getContext('2d')
      ctx.clearRect(0, 0, canvas.width, canvas.height)

      ctx.drawImage(img, 0, 0, canvas.width, canvas.height)
      url = canvas.toDataURL('image/jpeg')
      return url
    },
    dataURItoBlob(dataURI) {
      let byteString = window.atob(dataURI.split(',')[1])
      let mimeString = dataURI
        .split(',')[0]
        .split(':')[1]
        .split(';')[0]
      let ab = new ArrayBuffer(byteString.length)
      let ia = new Uint8Array(ab)
      for (let i = 0; i < byteString.length; i++) {
        ia[i] = byteString.charCodeAt(i)
      }
      return new window.Blob([ab], { type: mimeString })
    },
    add_img(event) {
      // console.log(this.updDir, 1)
      let file = event.target.files[0] //拿到图片信息
      if (/\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/.test(event.target.value)) {
        //判断图片信息后缀名
        let MAXSIZE = 10 * 1024 * 1024 //
        let size = file.size //图片大小
        if (size > MAXSIZE) {
          //图片大小如果超过限制就退出
          event.target.value = ''
          this.$notify.error({
            title: '上传图片错误',
            message: '上传图片不能超过10M',
          })
          return
        }
        this.loading = true
        let reader = new FileReader()
        let self = this
        reader.readAsDataURL(file)
        let img = new Image()
        let updDir = this.updDir
        reader.onload = function(e) {
          img.src = this.result
          img.onload = function() {
            let base = e.target.result
            let fileItem = file
            if (self.isCompress) {
              base = self.compress(img)
              fileItem = self.dataURItoBlob(base)
            }
            let formdata = new window.FormData()
            formdata.append('file', fileItem)
            axios({
              method: 'POST',
              url: '/upload/uploadImage/' + updDir,
              data: formdata,
              timeout: 1000000,
              headers: {
                'Content-Type': 'multipart/form-data',
              },
            })
              .then((res) => {
                event.target.value = ''
                self.photo.push({
                  fileName: file.fileName,
                  url: res.data.data,
                })
                self.loading = false
              })
              .catch((e) => {
                event.target.value = ''
                self.loading = false
                this.$message.error(e.message)
              })
          }
        }
      } else {
        event.target.value = ''
        this.$notify.error({
          title: '上传图片错误',
          message: '请上传gif|jpg|jpeg|png|GIF|JPG|PNG格式图片',
        })
      }
    },
  },
}
</script>
<style>
.finish_room {
  /*width: 140px;*/
  /*height: 165px;*/
}

img {
  height: 100%;
}

.finish_room2 {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  flex-flow: wrap;
}

.finish_room2 > div {
  display: flex;
  flex-wrap: wrap;
}

.finish_room2 .margeImg {
  margin-left: 10px;
}

.finish_room2 .room_img {
  width: 100px;
  height: 100px;
  cursor: pointer;
  overflow: hidden;
  position: relative;
  text-align: center;
  background-color: rgba(0, 0, 0, 0.5);
  margin-left: 10px;
  margin-top: 10px;
}

.finish_room2 .room_img:before {
  /*content: "";*/
  /*width: 0;*/
  /*height: 100%;*/
  /*!*background: #000;*!*/
  /*padding: 14px 18px;*/
  /*position: absolute;*/
  /*top: 0;*/
  /*left: 50%;*/
  /*opacity: 0;*/
}

.finish_room2 .room_img:hover:before {
  width: 100%;
  left: 0;
  opacity: 0.5;
}

.finish_room2 .room_img .box-content {
  width: 100%;
  padding: 14px 18px;
  color: #fff;
  position: absolute;
  top: 38%;
  left: 0;
}

.finish_room2 .room_img .icon {
  padding: 0;
  margin: 0;
  list-style: none;
  margin-top: -20px;
}

.finish_room2 .room_img .icon li {
  display: inline-block;
}

.finish_room2 .room_img .icon li i {
  display: block;
  width: 40px;
  height: 40px;
  line-height: 40px;
  border-radius: 50%;
  font-size: 20px;
  font-weight: 700;
  color: #fff;
  margin-right: 25px;
  opacity: 0;
  transform: translateY(50px);
  transition: all 0.5s ease 0s;
}

@media only screen and (max-width: 990px) {
  .finish_room2 .room_img {
  }
}

.finish_room2 .deleteImg {
  color: red;
  position: absolute;
  top: 0px;
  left: 80px;
  cursor: pointer;
}

.finish_room2 .room_img img {
  cursor: pointer;
  width: 100px;
  height: 100px;
}

.finish_room2 > .room_img span {
  position: absolute;
  width: auto;
  height: auto;
  right: 5px;
  bottom: 3px;
}

.finish_room2 .im-button {
  position: absolute;
  top: -14px;
  right: -18px;
  width: 40px;
  height: 40px;
  cursor: pointer;
  border-radius: 50%;
  background-color: rgba(0, 0, 0, 0.5);
  opacity: 0;
}

.finish_room2 .room_img:hover .icon li i {
  opacity: 0.5;
}

.finish_room2 .room_img:hover img {
  opacity: 0.5;
}

.finish_room2 .room_img:hover .im-button {
  opacity: 1;
}

.finish_room2 .im-close {
  transform: rotate(-45deg);
  line-height: 0px;
  left: 6px;
  bottom: 13px;
  display: inline-block;
  width: 15px;
  height: 2px;
  background: #fff;
  position: absolute;
}

.finish_room2 .im-close1 {
  left: 5px;
  bottom: 13px;
  display: inline-block;
  width: 15px;
  height: 2px;
  background: #fff;
  position: absolute;
  transform: rotate(45deg);
}

.room_add_img {
  margin-left: 10px;
  margin-top: 10px;
  width: 100px;
  height: 100px;
  cursor: pointer;
  border: 1px solid #e1e1e1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  position: relative;
  z-index: 10;
}

.room_add_img > span:nth-child(1) {
  width: 30px;
  height: 30px;
  overflow: hidden;
}

.room_add_img > span:nth-child(2) {
  margin-bottom: 10px;
}

.room_add_img input {
  cursor: pointer;
  position: absolute;
  top: 0px;
  left: 0px;
  width: 100%;
  height: 100%;
  z-index: 99999;
  opacity: 0;
}
</style>
