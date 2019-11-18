const app = getApp()
var index, poiInfo, mode
Page({

  data: {
    poiInfo: null,
    name: '',
    pmemo: '',
    poi: '',
    address: '',
    cat: '',
    imgUrl: app.globalData.config.imgUrl,
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
          pointid: that.data.poiInfo.pointid,
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
  fetch(ops) {
    wx.showLoading({
      title: '加载中',
    })
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getCatList',
      data: {
        openid: wx.getStorageSync('openid'),
        cat: ops.cat
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        let allcats = data.allcats
        let poi=data.list[index]
        this.setData({
          poiInfo: poi,
          poi: poi.latlng,
          address: poi.address,
          name: poi.name,
          cat: poi.cat,
          pmemo: poi.pmemo,
          allcats: allcats,
          pics: poi.pics.map((pic)=>{
            return pic.url
          })
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
  onLoad: function(options) {
    let ops = JSON.parse(options.ops)
    //console.log(ops)
    index = options.index
    mode = options.mode ? options.mode : 0
    console.log(ops)
    wx.setNavigationBarTitle({
      title: '点位设置',

    })

    this.fetch(ops)
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
  save() {
    let that = this
    let status = this.beforePost()
    let paths = that.data.pics
    if (status) {
      new Promise(that.postData).then(function(data) {
        //console.log(data)
        if (data.status) {
          if (paths && paths.length > 0) {


            for (let i in paths) {
              if (paths[i].indexOf('http://tmp/') > -1 || paths[i].indexOf('wxfile://') > -1) {
                wx.uploadFile({
                  url: app.globalData.config.apiUrl + 'uploadpointpic.php',
                  filePath: paths[i],
                  name: 'file',
                  formData: {
                    'pointid': that.data.poiInfo.pointid,
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
          wx.showToast({
            title: data.msg,
            icon: 'none'
          })
          let pages = getCurrentPages()
          let prepage = pages[pages.length - 2]
          let list = prepage.data.list
          list[index] = poiInfo
          console.log(list[index])
          if (mode == 1) {
            let markers = prepage.data.marker

            markers[index] = {
              'id': index,
              'alpha': 0.8,
              'latitude': poiInfo.latlng.split(',')[0],
              'longitude': poiInfo.latlng.split(',')[1],
              label: {
                anchorX: 10,
                anchorY: -20,
                color: '#f00',
                fontSize: 16,
                content: poiInfo.name
              }

            }
            prepage.setData({
              list: list,
              marker: markers
            })
          } else {
            // prepage.setData({
            //   list: list
            // })
            prepage.fetch()
          }
          setTimeout(() => {
            wx.navigateBack({
              delta: 1
            })
          }, 2000)
        } else {
          wx.showToast({
            title: data.msg,
            icon: 'none'
          })
        }

      }).catch(function(reason) {
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
      return false
    }
    let token = new Date().getTime();
    let cache = wx.getStorageSync('lastpost')
    wx.setStorageSync('lastpost', token)
    if (cache) {
      let duration = token - cache
      
      if (duration < 10000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none'
        })
        return false
      }

    }
    return true
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
  //位置定位
  selectPos() {
    let poiInfo = this.data.poiInfo
    let name = this.data.name
    //poiInfo.title=name
    let ops = {
      name: name,
      poi: poiInfo.poi,
      latlng: poiInfo.latlng
    }
    //console.log(ops)
    wx.navigateTo({
      url: './changePos?ops=' + JSON.stringify(ops),
    })
  },
  postData(resolve, reject) {
    let that = this
    poiInfo = that.data.poiInfo
    poiInfo.name = that.data.name
    poiInfo.pmemo = that.data.pmemo
    poiInfo.cat = that.data.cat
    poiInfo.address = that.data.address
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=editMyPos',
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