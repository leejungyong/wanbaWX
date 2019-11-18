const app=getApp()

Page({

  data: {
     poiInfo:null,
     name:'',
     pmemo:'',
     poi:'',
     address:'',
     cat:'',
     allcats:null,
    imgUrl:app.globalData.config.imgUrl,
    pics: null,
  },
  preview(e) {
    let id = e.currentTarget.id
    let pics = this.data.pics
    console.log(id)
    wx.previewImage({
      current: pics[id],
      urls: pics
    })
  },
  delPic(e) {
    let that = this
    let id = e.currentTarget.id
    console.log(id)
    let pics = this.data.pics
    console.log(pics[id])
    let pic = pics[id]
    if (pic.indexOf('http://tmp/') > -1 || pic.indexOf('wxfile://') > -1) {
      pics.splice(id, 1)
      this.setData({
        pics: pics
      })
    } else {
      wx.request({
        url: app.globalData.config.apiUrl + 'index.php?act=delPointPic',
        data: {
          index: id,
          picurl: pics[id],
          pointid: that.data.pointid,
          openid: wx.getStorageSync('openid')
        },
        method: 'POST',
        success: (res) => {

          let data = res.data
          console.log(data)
          if (data.status) {
            pics.splice(id, 1)
            this.setData({
              pics: pics
            })
            wx.showToast({
              title: data.msg,
              icon: 'none'
            })
          } else {
            wx.showToast({
              title: '删除图片失败，请重试',
              icon: 'none'
            })
          }
        },
        fail: (err) => {
          wx.showToast({
            title: '   网络错误',
            icon: 'none'
          })
        }
      })
    }

  },
  chooseImg() {
    let that = this
    let pics = that.data.pics
    let cnt = pics ? pics.length : 0
    let count = 9 - cnt
    wx.chooseImage({
      count: count,
      sizeType: ['compressed'],
      sourceType: ['album', 'camera'],
      success: function (res) {

        if (pics) {
          let paths = res.tempFilePaths
          for (let i in paths) {
            pics.push(paths[i])
          }

        } else {
          pics = res.tempFilePaths
        }
        that.setData({
          pics: pics
        })
        console.log(that.data.pics)
      }
    })
  },
  onLoad: function (options) {
    let data=JSON.parse(options.ops)
    console.log(data)
    wx.setNavigationBarTitle({
      title: '保存点位设置',
    })
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getAllCats',
      data: {
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let rst = res.data
       let  allcats = rst.allcats
        that.setData({
          poiInfo: data,
          poi: data.latlng,
          address: data.address,
          name: data.name,
          cat: data.cat,
          pmemo: data.pmemo,
          allcats:allcats
        })
        wx.hideLoading()
      },
      fail: (err) => {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
    
  },
  updateName(e) {
    this.setData({
      name: e.detail.value
    })
  },
  updatePmemo(e) {
    this.setData({
      pmemo: e.detail.value
    })
  },
  updateCat(e) {
    this.setData({
      cat: e.detail.value
    })
  },
  //设置分类
  setCat(e) {
    let id = e.currentTarget.id
    let cat = this.data.allcats[id].cat
    //console.log(cat)
    this.setData({
      cat: cat
    })
  },
  save(){
    let that = this
    let status=this.beforePost()
    let paths = that.data.pics
    if(status){
       new Promise(that.postData).then(function (data) {
         //console.log(data)
        if(data.status){
          if (paths && paths.length > 0) {


            for (let i in paths) {
              if (paths[i].indexOf('http://tmp/') > -1 || paths[i].indexOf('wxfile://') > -1) {
                wx.uploadFile({
                  url: app.globalData.config.apiUrl + 'uploadpointpic.php',
                  filePath: paths[i],
                  name: 'file',
                  formData: {
                    'pointid': data.pointid,
                    'openid': wx.getStorageSync('openid'),
                    'index': i
                  },
                  success: function (res) {
                    console.log(res)

                  }
                })
              }
            }


          }
          
          let pages=getCurrentPages()
          let prepage = pages[pages.length - 3]
          prepage.fetch()
          wx.showToast({
            title: data.msg,
            icon:'none'
          })
          setTimeout(()=>{
              wx.navigateBack({
                delta:2
              })
          },2000)
        }else{
          wx.showToast({
            title: data.msg,
            icon: 'none'
          })
        }

      }).catch(function (reason) {
           wx.showToast({
             title: reason,
             icon: 'none'
           })
      });
    }
  },
  beforePost() {
    let that = this
    if (that.data.name == '' || that.data.poi == '' || that.data.cat == '') {
      wx.showToast({
        title: '星号*为必填项',
        icon: 'none'
      })
      return  false
    }
    let token = new Date().getTime();
    let cache = wx.getStorageSync('lastpost')
    wx.setStorageSync('lastpost', token)
    if (cache) {
      let duration = token - cache
     
      if (duration < 3000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none'
        })
        return  false
      }

    }
    return true
  },
  //位置定位
  selectPos(){
    let poiInfo=this.data.poiInfo
    let name=this.data.name
    //poiInfo.title=name
    let ops = {
      name:name,
      poi: poiInfo.poi,
      latlng: poiInfo.latlng
    }
    console.log(ops)
     wx.navigateTo({
       url: './changePos?ops='+JSON.stringify(ops) ,
     })
  },
  postData(resolve, reject){
    let that = this
    let poiInfo=that.data.poiInfo
     poiInfo.name=that.data.name
    poiInfo.pmemo = that.data.pmemo
    poiInfo.cat = that.data.cat
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=addMyPos',
      data: {
        poiInfo: poiInfo,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        resolve(data);

      },
      fail: (err) => {
        reject('网络错误');
      }
    })
  }
})