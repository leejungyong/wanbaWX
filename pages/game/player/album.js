var aid, teamid, album, title, bonus, act_status = null

const app = getApp()
var picList = []
Page({
  data: {
    cdn: app.globalData.config.cdn,
    teams: null,
    currentTab: 0,
    album: null,
    list: [],
    cover: '1.jpg',
    imgUrl: app.globalData.config.cdn,
    imgheights: [],
    imgwidth: 750,
    imgwidth: 0,
    imgheight: 300,
    click: 0,
    total: 0,
    teamid: 0,
    bonus: 0,
    act_status: null,
    uploadlimited:false,
    showBigImage: false,
    dateTime: '', //当前图片的时间
    greatNum: 0,  //当前图片的点赞数
    imgurl: '',   //当前展示的图片地址
    currentpage: 0, //向后端请求的页码

    current: 1,     //swiper当前current
    page: 0,      //当前展示的图片页码
    numAll: 0    //图片总数
  },
  imageLoad: function (e) { //获取图片真实宽度  
    var imgwidth = e.detail.width,
      imgheight = e.detail.height,
      //宽高比  
      ratio = imgwidth / imgheight;
    //console.log(imgwidth, imgheight)
    //计算的高度值  
    var viewHeight = 750 / ratio;
    var imgheight = viewHeight;
    var imgheights = this.data.imgheights;
    imgheights[e.target.dataset.id] = imgheight;
    this.setData({
      imgheights: imgheights
    })
  },
  upload() {
    let that = this
    let content = that.data.bonus > 0 ? '1、每上传一张照片可为本队增加' + that.data.bonus + '点财富值\r\n2、严禁重复上传，或上传与本场活动无关的照片\r\n3、请遵守国家法律，严禁上传反动、淫秽内容的照片\r\n4、若有违反者，系统将删除有关违禁内容照片，并处以上传者所在队伍每张照片扣除50财富值之处罚\r\n5、欢迎互相监督举报，共同维护良好活动气氛' : '1、严禁重复上传，或上传与本场活动无关的照片\r\n2、请遵守国家法律，严禁上传反动、淫秽内容的照片\r\n3、若有违反者，系统将删除有关违禁内容照片，并处以上传者所在队伍每张照片扣除50财富值之处罚\r\n4、欢迎互相监督举报，共同维护良好活动气氛'
    wx.showModal({
      title: '上传规则',
      content: content,
      showCancel: false,
      success: (res) => {
        that.choosePics()
      }
    })
  },
  choosePics() {
    let that = this
    wx.chooseImage({
      count: 9,
      sizeType: ['original', 'compressed'],
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
      url: app.globalData.config.apiUrl + 'uploadalbum.php',
      filePath: arr[index],
      name: "file",
      formData: {
        aid: aid,
        teamid: teamid,
        bonus: bonus,
        openid: wx.getStorageSync('openid')
      },
      success: function (res) {
        console.log("上传成功")
        console.log(res.data)
        index++;

      },
      fail: (res) => {
        console.log("上传失败")
        console.log(res.data)

      },
      complete: function (res) {
        if (index == len) {
          that.fetch()
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

  onLoad: function (options) {
    let ops = JSON.parse(options.ops)
    aid = ops.aid
    teamid = ops.teamid
    act_status = ops.act_status

    this.fetch()
    this.fetchPic()
  },
  preview(e) {
    let id = e.currentTarget.id
    let picid = this.data.list[id].id
    let that = this

    let pics = this.data.list.map((item) => {
      return item.url
    })
    wx.previewImage({
      current: pics[id],
      urls: pics,
      success: (res) => {
        wx.request({
          url: app.globalData.config.apiUrl + 'index.php?act=updatePicView',
          data: {
            id: picid
          },
          method: 'POST',
          success: (ret) => {
            console.log(ret)
            let click = parseInt(that.data.click) + 1
            that.setData({
              click: click
            })
          },
          fail: (res) => {

          }
        })
      }
    })
  },

  //显示大图
  toBigImage(e) {
    let that = this
    let idx = parseInt(e.currentTarget.id)
    let curId = that.data.list[idx].id
    let curIndex = null

    picList.map((item, index) => {
      if (item.id == curId) {
        curIndex = index
      }
    })
    that.setData({
      showBigImage: true,
      page: curIndex,
      dateTime: picList[curIndex].date,
      greatNum: picList[curIndex].fav,
      imgurl: picList[curIndex].url
    })
  },
  //滑块改变时，改变时间
  changePic(e) {

    var that = this
    let current = e.detail.current
    let curpage = that.data.page

    if (current == 2) {//切换下一张时先判断是否是最后一张，是则始终保持当前状态
      if (curpage + 1 == that.data.numAll) {
        that.setData({
          current: 1
        })
      } else {
        curpage = curpage + 1
        that.setData({
          page: curpage,
          greatNum: picList[curpage].fav,
          dateTime: picList[curpage].date,
          imgurl: picList[curpage].url,
          current: 1
        })
      }
    } else if (current == 0) {//切换上一张时先判断是否是第一张，是则始终保持当前状态
      if (curpage == 0) {
        that.setData({
          current: 1
        })
      } else {
        curpage = that.data.page - 1
        that.setData({
          page: curpage,
          imgurl: picList[curpage].url,
          greatNum: picList[curpage].fav,
          dateTime: picList[curpage].date,
          current: 1
        })
      }
    }
  },
  //关闭大图
  closeImage() {
    this.setData({
      showBigImage: false
    })
  },

  //点赞
  great() {
    var that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=updatePicFav',
      data: {
        id: picList[that.data.page].id,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: res => {
        console.log(res)
        that.setData({
          greatNum: res.data.fav
        })
      }
    })
  },

  /** 图片下载 */
  downLoadPic() {
    let that = this
    wx.getSetting({
      success(res) {
        console.log(res.authSetting)
        if (res.authSetting['scope.writePhotosAlbum'] == undefined) {
          wx.authorize({
            scope: 'scope.writePhotosAlbum',
            success() {

            }
          })
        } else {
          if (!res.authSetting['scope.writePhotosAlbum']) {
            wx.showModal({
              title: '',
              showCancel: false,
              content: '如果要下载图片，请在设置中打开相册授权',
              success: (res) => {
                wx.openSetting({
                  success(res) { }
                })
              }
            })
          } else {
            wx.downloadFile({
              url: that.data.imgurl,
              success: res => {
                wx.saveImageToPhotosAlbum({
                  filePath: res.tempFilePath,
                  success(result) {
                    console.log(result)
                  }
                })
                wx.saveFile({
                  tempFilePath: res.tempFilePath,
                  success: function (res) {
                    console.log(res.savedFilePath)
                  }
                })
              }
            })
          }
        }

      }
    })


  },
  // navbarTap: function (e) {
  //   let idx = parseInt(e.currentTarget.dataset.idx) 
  //     console.log(idx)
  //   let list = idx == 0 ? album : album.filter((pic, index) => {
  //     return parseInt(pic.teamid)  == idx
  //   })
  //   console.log(list)
  //   this.setData({
  //     currentTab: idx,
  //     list: list
  //   })
  // },

  navbarTap: function (e) {
    let idx = e.currentTarget.dataset.idx

    this.setData({
      currentpage: 0,
      currentTab: idx,
      list: []
    })
    this.fetchTeam()
  },
  fetchTeam() {
    wx.showLoading({
      title: '加载中...',
    })
    console.log(this.data.currentTab)
    // console.log(this.data.currentpage)
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getAlbumTest',
      method: 'POST',
      data: {
        aid: aid,
        currentpage: that.data.currentpage,
        teamid: that.data.currentTab,
        myteam: teamid
      },
      success: res => {
        console.log(res.data)
        let arr = that.data.list.length == 0 ? res.data.album : that.data.list.concat(res.data.album)
        that.setData({
          list: arr
        })
        wx.hideLoading()
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  fetch() {
    let that = this
    wx.showLoading({
      title: '加载中',
    })
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getAlbumTest',
      data: {
        aid: aid,
        currentpage: that.data.currentpage,
        myteam:teamid
      },
      method: 'POST',
      success: (res) => {
        console.log(res.data)
        let data = res.data
        let addData = {
          name: '全部',
          displayorder: 0
        }
        let teams = data.teams
        title = data.title
        teams.unshift(addData)
        let arr = that.data.list.length == 0 ? data.album : that.data.list.concat(data.album)
        // let album = data.album
        bonus = data.bonus
        that.setData({
          total: data.total,
          teams: teams,
          list: arr,
          cover: data.cover,
          click: data.click,
          teamid: teamid,
          bonus: data.bonus,
          uploadlimited: data.uploadlimited,
          act_status: act_status
        })
        wx.hideLoading()
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  //获取全部图片 用作swiper
  fetchPic() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getAlbum',
      method: 'POST',
      data: {
        aid: aid
      },
      success: res => {
        if (res.data.album.length > 0) {
          console.log(res.data)
          let album = res.data.album
          let arr = album.map(value => {
            let d = new Date(value.date * 1000)
            if (isNaN(d)) {
              return value
            } else {
              let y = d.getFullYear()
              let m = d.getMonth() + 1
              let date = d.getDate()
              let h = d.getHours()
              let min = d.getMinutes()
              let s = d.getSeconds()
              value.date = y + '.' + m + '.' + date + ' ' + h + ':' + min + ':' + s
              return value
            }
          })
          picList = arr
          that.setData({
            numAll: picList.length
          })
          // console.log(picList)
        }
      }
    })
  },


  onPullDownRefresh: function () {

    wx.showNavigationBarLoading();
    this.setData({
      currentpage: 0,
      list: []
    })
    if (this.data.currentTab == 0) {
      this.fetch()
    } else {
      this.fetchTeam()
    }

    wx.hideNavigationBarLoading();
    wx.stopPullDownRefresh()
  },


  onReachBottom: function () {
    let that = this
    that.setData({
      currentpage: that.data.currentpage + 1
    })
    if (that.data.currentTab == 0) {
      that.fetch()
    } else {
      that.fetchTeam()
    }

  },

  onShareAppMessage: function (ops) {
    let that = this
    let stamp = new Date().getTime()
    let op = {
      aid: aid,
      teamid: 0
    }
    if (ops.from === 'button') {
      return {
        title: title + '照片直播',
        path: 'pages/game/player/album?ops=' + JSON.stringify(op),
        imageUrl: app.globalData.config.apiUrl + 'sharepic/' + that.data.cover + '?' + stamp,
        success: function (res) {
        },
        fail: function (res) {
        }
      }
    }
    return {
      title: title + '照片直播',
      path: 'pages/game/player/album?ops=' + JSON.stringify(op),
      imageUrl: app.globalData.config.apiUrl + 'sharepic/' + that.data.cover + '?' + stamp,
      success: function (res) {
        // 转发成功


      },
      fail: function (res) {

      }
    }
  },
})