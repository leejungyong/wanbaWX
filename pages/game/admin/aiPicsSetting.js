const app=getApp()
var aid=0
Page({


  data: {
   imgwidth: 750,
    imgheight: 160,
    arr:[]
  },


  onLoad: function (options) {
    aid=options.aid
    this.fetch()
  },
  fetch(){
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getaipics',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        that.setData({
          arr:data
        })
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }

    })
  },
  aicapture(){
     wx.navigateTo({
       url: './aicapture?aid='+aid,
     })
  },
  choosePics() {
    let that = this


    wx.chooseImage({
      count: 9,
      sizeType: ['compressed'],
      sourceType: ['album', 'camera'],
      success: function (res) {
        let pics = res.tempFilePaths
        
       that.uploadPic(0, that, pics)

      }
    })

  },
  uploadPic: (index, that, arr) => {
    var len = arr.length

    var upload_task = wx.uploadFile({
      url: app.globalData.config.apiUrl + 'uploadaipic.php',
      filePath: arr[index],
      name: "file",
      formData: {
         aid: aid,
         openid: wx.getStorageSync('openid')
      },
      success: function (res) {
        console.log("上传成功")
        //console.log(res.data)
        let data=JSON.parse(res.data)
        let arr=that.data.arr
        arr.unshift(data)
        that.setData({
          arr:arr
        })
        index++;

      },
      fail: (res) => {
        console.log("上传失败")
        console.log(res.data)

      },
      complete: function (res) {
        if (index == len) {
         
          console.log(index)
          wx.showToast({
            title: '上传完成',
            icon: 'success',
            duration: 2000
          })
        } else {
          console.log("长度小于数组长度")
          console.log('正在上传第' + index + '张');
          that.uploadPic(index, that, arr) //递归

        }
      }
    })
  },
  picLoad: function (e) {
    var _this = this;
    var $width = e.detail.width, //获取图片真实宽度
      $height = e.detail.height,
      ratio = $width / $height; //图片的真实宽高比例
    var viewWidth = 640, //设置图片显示宽度，
      viewHeight = 640 / ratio; //计算的高度值   
    this.setData({
      imgwidth: viewWidth,
      imgheight: viewHeight
    })

  },
  delPic(e){
    let that=this
    let index=e.currentTarget.id 
    //console.log(this.data.arr[index])
    let id=that.data.arr[index].id
    wx.showModal({
      title: '警告',
      content: '确定要删除吗',
      success:((res)=>{
        if(res.confirm){
          wx.request({
            url: app.globalData.config.apiUrl + 'index.php?act=delaipic',
            data: {
              aid: aid,
              openid: wx.getStorageSync('openid'),
              id: id
            },
            method: 'POST',
            success: (res) => {
              let data = res.data
             // console.log(data)
             if(data.status){
               let arr=that.data.arr
               arr.splice(index, 1)
               that.setData({
                 arr:arr
               })
             }
             wx.showToast({
               title: data.msg,
               icon:'none'
             })
            },
            fail: (res) => {
              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }

          })
        }
      })
    })
   
  }
 
})