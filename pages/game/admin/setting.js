var aid = null

const app = getApp()

Page({

  data: {

    lands: null,
    act: null,
    coach: null,

  },

  pcAdmin() {
    wx.navigateTo({
      url: './pcAdmin',
    })
  },
  albumAdmin() {
    wx.navigateTo({
      url: './albumAdmin?aid=' + aid,
    })
  },

  onLoad: function (options) {
    aid = options.aid
    console.log(aid)
    this.fetch()
  },
  baseSetting() {
    if (this.data.act.mode == 2) {
      wx.showToast({
        title: '活动已开始，无法再进行编辑',
        icon:'none'
      })
    } else {
      wx.navigateTo({
        url: './baseSetting?aid=' + aid,
      })
    }
  },
  redbagSetting() {
    wx.navigateTo({
      url: './redbagSetting?aid=' + aid,
    })
  },
  importTemplate() {
    wx.navigateTo({
      url: './importTemplate?aid=' + aid,
    })
  },
  radioChange(e) {

    let v = e.detail.value
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=updateActStatus',
      data: {
        aid: aid,
        status: v
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        //console.log(data)
        wx.showToast({
          title: data.msg,
          icon: 'none'
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
  //点位任务设置
  poiSetting(e) {
    let id = e.currentTarget.id
    let cat=this.data.act.cat
    let poiInfo = this.data.lands[id]
    let str = JSON.stringify(poiInfo)
    str = str.replace(/\?/g, '？')
    str = str.replace(/\&/g, '＆')
    wx.navigateTo({
      url: './poiSetting?data=' + str+'&aid='+aid+'&cat='+cat

    })
  },
  fetch() {


    wx.showLoading({
      title: '加载中',
    })
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=actSetting',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        let tasks = data.task


        var temp = []
        for (let i = 0; i < 49; i++) {
          var ptype = 0
          var j = i + 1
          if (j == 25) {
            ptype = 2
          } else if (j == 1 || j == 7 || j == 9 || j == 13 || j == 17 || j == 19 || j == 31 || j == 33 || j == 37 || j == 41 || j == 43 || j == 49) {
            ptype = 1
          }
          let item = {
            taskid: '',
            aid: aid,
            name: '',
            memo: '',
            poi: '',
            pmemo: '',
            displayorder: j,
            answer: '',
            ptype: ptype,
            gps: 0,
            open: 0,
            pics: [],
            media: '',
            url: '',
            qtype: '',
            latlng: ''
          }
          temp.push(item)
        }
        for (let i in temp) {
          for (let j in tasks) {
            if (tasks[j].displayorder - 1 == i) {
              temp[i] = tasks[j]
            }
          }
        }
        console.log(temp)
        wx.hideLoading()

        var gps
        if (data.act.gpsEnabled == 1) {
          gps = data.act.offset
        } else {
          gps = 0
        }

        this.setData({
          lands: temp,
          //lands: data.task,
          // myteam: data.myteam,
          act: data.act,
          // teams: data.teams,
          // items: items,
          coach: data.act.coach,
          pvalue: data.act.pvalue,
          gps: gps,
          mineNum: data.act.minenum,
          mineMoney: data.act.minevalue
        })
        //console.log(this.data.lands)
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


  theme() {
    if (parseInt(this.data.act.mode == 2)) {
      wx.showModal({
        title: '提示',
        content: '活动已经正式开始，无法进行门派自定义！',
        showCancel: false
      })
    } else {
      wx.navigateTo({
        url: './themeContent?themeid=' + this.data.act.teamThemeId + '&aid=' + aid,
      })
    }

  },
  onPullDownRefresh: function () {
    wx.showNavigationBarLoading();
    this.fetch()
    wx.hideNavigationBarLoading();
    wx.stopPullDownRefresh()
  },

  stoneSetting() {
    wx.navigateTo({
      url: './stoneList?aid=' + aid + '&act_title=' + this.data.act.title + '&cat=' + this.data.act.cat,
    })
  },
  configSetting() {

    wx.navigateTo({
      url: './config?aid=' + aid,
    })
  },
  coachSetting() {
    wx.navigateTo({
      url: './coachSetting?aid=' + aid + '&coach=' + this.data.coach,
    })
  },



  onShareAppMessage: function (ops) {
    let that = this
    let stamp = new Date().getTime()
    //console.log(ops)
    if (ops.from === 'button') {
      return {
        title: '邀请你成为' + that.data.act.title + '的管理员',
        path: 'pages/game/admin/promoteManager?aid=' + aid,
        imageUrl: app.globalData.config.apiUrl + 'sharepic/' + that.data.act.sharepic + '?' + stamp,
        success: function (res) {

        },
        fail: function (res) {

        }
      }
    }
    return {
      title: '',
      path: 'pages/game/player/main?aid=' + aid,
      imageUrl: app.globalData.config.apiUrl + 'sharepic/' + that.data.act.sharepic + '?' + stamp,
      success: function (res) {
        // 转发成功


      },
      fail: function (res) {

      }
    }
  },
  sysInit() {
    let that = this
    wx.showModal({
      title: '严重警告',
      content: '此操作会清空所有数据，确定要执行操作吗？',
      success(res) {
        if (res.confirm) {
          wx.request({
            url: app.globalData.config.apiUrl + 'index.php?act=sysInit',
            data: {
              aid: aid
            },
            method: 'POST',
            success: (res) => {
              let data = res.data
              //console.log(data)
              if (data.status) {
                wx.showToast({
                  title: data.msg,
                })
                setTimeout(() => {
                  that.fetch()
                }, 2000)
              }
              else {
                wx.showToast({
                  title: '   出错了',
                })
              }
            },
            fail: (res) => {

            }
          })
        } else if (res.cancel) {
          console.log('用户点击取消')
        }
      }
    })
  }
})