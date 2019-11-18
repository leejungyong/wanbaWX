const app = getApp()

Page({

  _data: {
    aid: 0,
    teamid: 0,
    teamname: '',
    act:null
  },
  data: {
    isCaptain: false,
    stoneSelected:null,
    hideStone:true,
    imgUrl:app.globalData.config.imgUrl
  },


  onLoad: function(options) {
    let ops = JSON.parse(options.ops)
    let act = JSON.parse(options.act)
   
    console.log(act)
    this._data.aid = ops.currentaid
    this._data.teamid = ops.currentteamid
    this._data.teamname = ops.name
    this.setData({
      isCaptain: ops.currentrole > 0 ? false : true,
      act:act
    })

  },
  hideStone() {
    this.setData({
      hideStone: true
    })
  },

  scanStone() {

    let that = this
    wx.scanCode({
      onlyFromCamera: false,
      success: (res) => {
        let result = res.result
        // console.log(result)
        let stone = result.split('&')[0]
        let aid = result.split('&')[1]
        let token = result.split('&')[2]
        stone = stone.split('=')[1]
        aid = aid.split('=')[1]
        token = token.split('=')[1]
        if (stone && aid == that._data.aid && token) {
          wx.request({
            url: app.globalData.config.apiUrl+'index.php?act=getStone',
            data: {
              stone: stone,
              teamid: that._data.teamid,
              teamname: that._data.teamname,
              aid: that._data.aid,
              token: token
            },
            method: 'POST',
            success(res) {
              console.log(res.data)
              let data=res.data
              if(data){
                that.setData({
                  stoneSelected:data,
                  hideStone:false
                })
              }

            },
            fail(res) {
              wx.hideLoading()
              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }
          })
        }
      }
    })

  },
  qrcode() {
    let ops = {
      aid: this._data.aid,
      teamid: this._data.teamid,
      act: 'addMoney',
      teamname: this._data.teamname,
      openid: wx.getStorageSync('openid')
    }
    wx.navigateTo({
      url: './showmycode?ops=' + JSON.stringify(ops),
    })

  },
  album(){
    let ops = {
      aid: this._data.aid,
      teamid: this._data.teamid
    }

    wx.navigateTo({
      url: './album?ops=' + JSON.stringify(ops)
    })
  },
  radar() {
    let ops = {
      aid: this._data.aid,
      teamid: this._data.teamid
    }

    wx.navigateTo({
      url: './radar?ops=' + JSON.stringify(ops)
    })
  },
  werun() {
    let ops = {
      aid: this._data.aid,
      teamid: this._data.teamid
    }

    wx.navigateTo({
      url: './werun?ops=' + JSON.stringify(ops)
    })
  },
  quit() {
    let that = this
    wx.showModal({
      title: '确定要退出队伍吗？',
      content: '',
      success(res) {
        if (res.confirm) {
          wx.request({
            url: app.globalData.config.apiUrl+'index.php?act=quitteam',
            data: {
              aid: that._data.aid,
              teamid: that._data.teamid,
              openid: wx.getStorageSync('openid')
            },
            method: 'POST',
            success: (res) => {
              //console.log(res)
              let data = res.data
              if (data.status) {
                wx.showToast({
                  title: '你已退出该队',
                  icon: 'none',
                  success: (res) => {
                    setTimeout(() => {
                      wx.reLaunch({
                        url: './main?aid=' + that._data.aid,
                      })
                    }, 2000)
                  }
                })

              } else {
                wx.showToast({
                  title: '错误',
                  icon: 'none'
                })
              }
            },
            fail: (res) => {
              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }
          })
        }
      }
    })
  },
  captain() {
    
    let ops = {
      act: 'isCaptain',
      openid: wx.getStorageSync('openid'),
      teamid: this._data.teamid,
      teamname:this._data.teamname,
      aid: this._data.aid
    }
    wx.navigateTo({
      url: './showcode?ops=' + JSON.stringify(ops),
    })
  },
  sms() {
    let key = 'isCoach_' + this._data.aid
    if (wx.getStorageSync(key)) {

      wx.navigateTo({
        url: './judge',
      })
    } else {
      wx.navigateTo({
        url: './sms?aid=' + this._data.aid
      })
    }
  },
  viewTeam() {
    let role = this.data.isCaptain ? 0:1
    wx.navigateTo({
      url: './viewteam?ops=' + JSON.stringify(this._data)+'&role='+role,
    })
  },

})