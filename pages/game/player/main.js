var audioCtx
const app = getApp()
Page({
  _data: {
    aid: 0,
    teamid: 0,
    actStatus: -1,
    title: '',
    countTime: '',
  },
  data: {
    audio: 'https://img.wondfun.com/wanba/img/redbag/godlike.mp3',
    showTime: false,
    timer: '', //定时器
    stamp: new Date().getTime(),
    slogan: '让世界更好玩',
    imgUrl: app.globalData.config.imgUrl,
    imgUrlLogo: app.globalData.config.imgUrl + 'wanba/api/logopic/default.jpg',
    godview: false,
    inputTxt: '',
    auctionTxt: '',
    lands: null,
    myteam: null,
    act: null,
    teams: null,
    teams_nomyteam: null,
    flag: true,
    hidden: true,
    hideBox: true,
    hideStone: true,
    hideUseStone: true,
    hideWanba: true,
    wanbaMsg: '',
    teamvalue: [0],
    selectedteam: 1,
    landIdhold: '',
    landIdexchange1: '',
    landIdexchange2: '',
    stones: null,
    stoneSelected: null,
    currentPos: {
      displayorder: null,
      taskid: null,
      pvalue: null,
    }
  },
  onReady: function() {
    audioCtx = wx.createAudioContext('myAudio')

  },
  //地块连线
  link() {
    let myteamid = this.data.myteam.currentteamid,
      aid = this.data.act.aid
    wx.navigateTo({
      url: './toAccount?' + '&myteamid=' + myteamid + '&aid=' + aid,
    })
  },
  playAudio(mp3) {

    audioCtx.setSrc(mp3) //音频文件，第三方的可自行选择
    audioCtx.play() //播发音频
  },
  shake() {
    wx.navigateTo({
      url: './shake?aid=' + this._data.aid,
    })
  },
  AI() {
    let that = this
    wx.getSetting({
      success(res) {
        console.log(res.authSetting)
        if (res.authSetting['scope.camera'] == undefined) {
          wx.authorize({
            scope: 'scope.camera',
            success() {}
          })
        } else {
          if (!res.authSetting['scope.camera']) {
            wx.showModal({
              title: '',
              showCancel: false,
              content: '要使用此功能，请在设置中打开摄像头授权',
              success: (res) => {
                wx.openSetting({
                  success(res) {}
                })
              }
            })
          } else {
            wx.navigateTo({
              url: './ai?aid=' + that._data.aid + '&teamid=' + that.data.myteam.currentteamid,
            })
          }
        }
      }
    })

  },
  album() {
    let ops = {
      aid: this._data.aid,
      teamid: this.data.myteam.currentteamid,
      act_status: this.data.act.status
    }
    console.log(ops)
    wx.navigateTo({
      url: './album?ops=' + JSON.stringify(ops)
    })
  },
  topBoard() {
    wx.navigateTo({
      url: '../admin/topBoard?aid=' + this._data.aid,
    })
  },
  closeWanba() {
    this.setData({
      hideBox: true,
      hideWanba: true
    })
  },
  getWanba() {
    let that = this
    let myteam = that.data.myteam
    let token = new Date().getTime();
    let cache = wx.getStorageSync('lastpost_getwanba')
    wx.setStorageSync('lastpost_getwanba', token)
    if (cache) {
      let duration = token - cache

      if (duration < 3000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none'
        })
        return false
      } else {
        wx.request({
          url: app.globalData.config.apiUrl + 'index.php?act=getWanba',
          data: {
            aid: myteam.aid,
            myteam: myteam.displayorder,
            myteamname: myteam.name
          },
          method: 'POST',
          success: (res) => {
            let data = res.data
            if (data.status) {
              that.setData({
                hideWanba: false,
                wanbaMsg: data.msg
              })
              that.playAudio(that.data.audio)
            } else {
              wx.showToast({
                title: '操作错误',
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

    } else {
      wx.request({
        url: app.globalData.config.apiUrl + 'index.php?act=getWanba',
        data: {
          aid: myteam.aid,
          myteam: myteam.displayorder,
          myteamname: myteam.name
        },
        method: 'POST',
        success: (res) => {
          let data = res.data
          if (data.status) {
            that.setData({
              hideWanba: false,
              wanbaMsg: data.msg
            })
            that.playAudio(that.data.audio)
          } else {
            wx.showToast({
              title: '操作错误',
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

  },
  onLoad: function(options) {
    this._data.aid = options.aid
    this.fetch()
  },
  homepage() {
    wx.switchTab({
      url: '/pages/index/index'
    })
  },
  log() {
    wx.navigateTo({
      url: './log?aid=' + this._data.aid + '&teamid=' + this.data.myteam.currentteamid,
    })
  },
  updateAuctiontxt(e) {
    this.setData({
      auctionTxt: e.detail.value
    })
  },
  updateTxt(e) {
    this.setData({
      inputTxt: e.detail.value
    })
  },
  cancelTrade() {
    this.setData({
      flag: true
    })
  },
  cancelAuction() {
    this.setData({
      hidden: true
    })
  },
  confirmAuction() {

    if (this.data.auctionTxt == '' || isNaN(parseInt(this.data.auctionTxt)) || parseInt(this.data.auctionTxt) <= parseInt(this.data.currentPos.pvalue)) {
      wx.showToast({
        title: '起拍价不得低于' + this.data.currentPos.pvalue,
        icon: 'none'
      })
    } else if (parseInt(this.data.auctionTxt) > parseInt(this.data.myteam.money)) {
      wx.showToast({
        title: '钱好像不太够哦',
        icon: 'none'
      })
    } else {
      let ops = {
        act: 'auction',
        taskid: this.data.currentPos.taskid,
        aid: this.data.act.aid,
        openid: wx.getStorageSync('openid'),
        teamid: this.data.myteam.currentteamid,
        sellprice: this.data.auctionTxt,
        posid: this.data.currentPos.displayorder,
        teamname: this.data.myteam.name
      }
      wx.navigateTo({
        url: './showauctioncode?ops=' + JSON.stringify(ops),
      })
    }
  },
  confirmTrade() {

    if (this.data.inputTxt == '' || isNaN(parseInt(this.data.inputTxt)) || parseInt(this.data.inputTxt) <= this.data.currentPos.pvalue) {
      wx.showToast({
        title: '请设置不低于' + this.data.currentPos.pvalue + '的价格',
        icon: 'none'
      })
    } else {
      let ops = {
        act: 'sell',
        taskid: this.data.currentPos.taskid,
        aid: this.data.act.aid,
        openid: wx.getStorageSync('openid'),
        teamid: this.data.myteam.currentteamid,
        sellprice: this.data.inputTxt,
        posid: this.data.currentPos.displayorder,
        teamname: this.data.myteam.name
      }
      // console.log(ops)
      wx.navigateTo({
        url: './showtradecode?ops=' + JSON.stringify(ops),
      })
    }
  },
  hide() {
    this.setData({
      flag: true,
      hidden: true
    })
  },
  team() {

    if (this.data.myteam.length == 0) {
      wx.navigateTo({
        url: './join?aid=' + this._data.aid,
      })
    } else {
      console.log(this.data.myteam)
      let ops = JSON.stringify(this.data.myteam)
      ops = ops.replace(/\?/g, '？')
      ops = ops.replace(/\&/g, '＆')
      let act = JSON.stringify(this.data.act)
      act = act.replace(/\?/g, '？')
      act = act.replace(/\&/g, '＆')
      wx.navigateTo({
        url: './team?ops=' + ops + '&act=' + act,
      })
    }
  },
  //拍卖
  auction: function(e) {
    console.log(e)
    let aid = this._data.aid
    let that = this
    let id = e.currentTarget.id
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getActStatus',
      data: {
        aid: aid
      },
      method: 'POST',
      success: (res) => {
        if (res.data != 4) {
          wx.showToast({
            title: '拍卖尚未开放',
            icon: 'none'
          })
          return
        } else {


          if (that.data.lands[id].owner.indexOf(that.data.myteam.currentteamid) >= 0 && that.data.myteam.currentrole == 0) {
            if (parseInt(that.data.myteam.money) <= parseInt(that.data.lands[id].pvalue)) {
              wx.showToast({
                title: '此地产起拍价不低于' + that.data.lands[id].pvalue + ',您只有财富' + that.data.myteam.money,
                icon: 'none'
              })
              return
            }
            that.setData({
              hidden: false,
              currentPos: {
                pvalue: that.data.lands[id].currentPvalue,
                taskid: that.data.lands[id].taskid,
                displayorder: that.data.lands[id].displayorder
              }
            })
          }
        }
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })

  },
  getActStatus() {
    let aid = this._data.aid
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getActStatus',
      data: {
        aid: aid
      },
      method: 'POST',
      success: (res) => {
        that._data.actStatus = res.data
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  syncWeRunData(aid, teamid) {

    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=updateMyStep',

      data: {
        openid: wx.getStorageSync('openid'),
        aid: that._data.aid,
        teamid: that.data.myteam.currentteamid,
        step: that._data.myStep
      },
      method: 'POST',
      success(res) {
        // console.log(res.data)
        // wx.showToast({
        //   title: res.data.step.toString(),
        //   icon: 'none'
        // })
      }
    })
  },

  getWeRunData() {
    let that = this
    if (that.data.myteam) {
      wx.getWeRunData({ //解密微信运动
        success(res) {
          //console.log(res)
          const wRunEncryptedData = res.encryptedData
          wx.request({
            url: app.globalData.config.apiUrl + 'decrypt/decrypt.php',
            header: {
              'cache-control': 'no-cache'
            },
            data: {
              iv: res.iv,
              encryptedData: wRunEncryptedData,
              session_key: wx.getStorageSync('session_key')
            },
            method: 'POST',
            success(res) {
              let data = JSON.parse(res.data)
              let myStep = data.stepInfoList
              // console.log(myStep)
              if (myStep) {
                let todayStep = myStep[myStep.length - 1].step
                // that.setData({
                //   myStep: todayStep
                // });
                // console.log(aid, teamid)
                that._data.myStep = todayStep
                let aid = that._data.aid
                let teamid = that.data.myteam.currentteamid
                if (aid > 0) {
                  setTimeout(function() {

                    that.syncWeRunData(aid, teamid)
                  }, 100)

                }
              }

            },
            fail(err) {
              console.log(err)
            }
          })
        },
        fail(err) {

          wx.navigateTo({
            url: './authwerun',
          })
        }
      })
    }
  },

  fetch() {
    let aid = this._data.aid

    wx.showLoading({
      title: '加载中',
    })
    let that = this
    // console.log(wx.getStorageSync('openid'))
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=actInfo',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data

        console.log(data)

        if (parseInt(res.data.act.status) == 0 && parseInt(res.data.act.endTime) > 0 && (res.data.act.endTime * 1000) > (new Date().getTime())) {
          let time = data.act.endTime * 1000 - new Date().getTime()
          console.log(time)
          that.setData({
            showTime: true,
            timer: setInterval(function() {
              time = time - 99;
              let hour = parseInt(time / (60 * 60 * 1000))
              if (hour < 10 && hour >= 0) {
                hour = '0' + hour
              }
              let afterHour = time - hour * 60 * 60 * 1000; //取得算出小时数后剩余的秒数
              let min = parseInt(afterHour / (60 * 1000))
              if (min < 10 && min >= 0) {
                min = '0' + min
              }
              let afterMin = parseInt((time - hour * 60 * 60 * 1000 - min * 60 * 1000) / 1000);


              let msecond = time % 100
              msecond = msecond < 10 ? '0' + msecond : msecond
              if (afterMin >= 0 && afterMin < 10) {
                afterMin = '0' + afterMin
              }
              that.setData({
                countTime: hour + ':' + min + ':' + afterMin + ':' + msecond
              })
              if (parseInt(time) <= 0) {
                clearInterval(that.data.timer)
                that.setData({
                  showTime: false
                })
                return
              }
            }, 99)
          })


        } else {
          that.setData({
            showTime: false
          })
        }
        let tasks = data.task
        // that._data.werun = data.act.werun
        for (let i in tasks) {
          //tasks[i].currentowner = tasks[i].owner 
          let owners = tasks[i].owner ? tasks[i].owner.split(',') : []
          tasks[i].owner = owners

        }
        wx.setNavigationBarTitle({
          title: data.act.title,
        })
        that._data.title = data.act.title
        wx.hideLoading()
        let timestamp = Date.parse(new Date()) / 1000;
        let godview = false
        if (data.myteam) {
          godview = timestamp < data.myteam.godview ? true : false;
        }
        let teams = data.teams
        let temp = new Array()
        for (let i in teams) {
          temp[i] = teams[i]
        }
        let myteam = data.myteam
        // console.log(myteam)
        temp.splice(myteam.displayorder - 1, 1)
        let teams_nomyteam = temp
        //console.log(teams_nomyteam)
        let stamp = new Date().getTime()
        this.setData({
          lands: data.task,
          myteam: myteam,
          act: data.act,
          teams: teams,
          teams_nomyteam: teams_nomyteam,
          godview: godview,
          slogan: data.act.slogan || '让世界更好玩',
          imgUrlLogo: data.act.logopic ? app.globalData.config.apiUrl + 'logopic/' + data.act.logopic + '?' + stamp : app.globalData.config.apiUrl + 'logopic/default.jpg'
        })
        // console.log(that.data)
        let werun = data.act.werun
        //console.log(werun)
        if (werun == 1) {
          wx.getSetting({
            success(res) {
              // console.log(res)
              if (!res.authSetting['scope.werun']) {
                wx.authorize({
                  scope: 'scope.werun',
                  success() {

                  },
                  fail() {
                    wx.navigateTo({
                      url: './authwerun',
                    })
                  }
                })
              } else {
                //console.log('auth')
              }
            },
            fail(res) {
              console.log(res)
            }
          })
        }
        if (wx.getStorageSync('openid') == data.act.creator) {
          wx.navigateTo({
            url: '../admin/main?aid=' + aid + '&title=' + that._data.title + '&cat=' + data.act.cat,
          })
        }
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
  //交易
  deal: function(e) {
    let id = e.currentTarget.id
    let aid = this._data.aid
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getActStatus',
      data: {
        aid: aid
      },
      method: 'POST',
      success: (res) => {

        if (res.data != 2) {
          wx.showToast({
            title: '已过可以交易期',
            icon: 'none'
          })
          return
        } else {



          if (that.data.lands[id].owner == that.data.myteam.currentteamid && that.data.myteam.currentrole == 0) {
            this.setData({
              flag: false,
              currentPos: {
                pvalue: that.data.lands[id].currentPvalue,
                taskid: that.data.lands[id].taskid,
                displayorder: that.data.lands[id].displayorder
              }
            })
            //console.log(that.data.currentPos)
          }
        }
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })


  },



  map: function(e) {
    let that = this

    if (this.data.act.status == -1) {
      wx.showToast({
        title: '游戏还没开始',
        icon: 'none'
      })
      return
    }

    if (this.data.myteam.length == 0) {
      // wx.showToast({
      //   title: '请先加队',
      //   icon: 'none'
      // })
      that.team()
    } else {

      let id = e.currentTarget.id
      let act = JSON.stringify(this.data.act)
      act = act.replace(/\?/g, '？')
      act = act.replace(/\&/g, '＆')
      //console.log(act)
      let ops = JSON.stringify(this.data.lands[id])
      console.log(ops)
      let open = this.data.lands[id].open
      if (open == 1) {
        wx.showToast({
          title: '此点位未开放',
          icon: 'none'
        })
        return
      }
      ops = ops.replace(/\?/g, '？')
      ops = ops.replace(/\&/g, '＆')
      //console.log(ops)
      let teamid = this.data.myteam.currentteamid
      let slogan = that.data.slogan
      slogan = slogan.replace(/\?/g, '？')
      slogan = slogan.replace(/\&/g, '＆')
      console.log(slogan)
      wx.navigateTo({
        url: './map?act=' + act + '&ops=' + ops + '&roleid=' + this.data.myteam.currentrole + '&slogan=' + slogan + '&teamid=' + teamid + '&teamname=' + this.data.myteam.name
      })
    }
  },
  //买地
  buy() {
    let aid = this._data.aid
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getActStatus',
      data: {
        aid: aid
      },
      method: 'POST',
      success: (res) => {
        if (res.data != 2) {
          wx.showToast({
            title: '已过可以交易期',
            icon: 'none'
          })
          return
        } else {
          if (that.data.myteam.currentrole > 0) {
            wx.showToast({
              title: '无权操作',
              icon: 'none'
            })
            return
          }
          wx.navigateTo({
            url: './buy?aid=' + that._data.aid
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
  syncUser() {
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=addUser',
      data: {
        openid: wx.getStorageSync('openid'),
        unionid: wx.getStorageSync('unionid')
      },
      method: 'POST',
      success: (res) => {
        //console.log(res)
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  wxLogin() {
    let that = this
    wx.login({
      success(res) {
        if (res.code) {

          wx.request({
            url: app.globalData.config.userApiUrl + '?code=' + res.code,
            data: {},

            success: (res) => {
              let data = res.data
              let openid = data.openid
              let unionid = data.unionid
              //console.log(openid)
              wx.setStorageSync('session_key', data.session_key)
              //console.log(wx.getStorageSync('session_key'))
              wx.setStorageSync('unionid', unionid)
              // that.getWeRunData()
              if (openid) {
                wx.setStorageSync('openid', openid)
                // console.log(wx.getStorageSync('openid'))
                that.syncUser()

              } else {
                console.log('网络错误')
              }

            },
            fail: (res) => {
              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }
          })
        } else {
          console.log('登录失败！' + res.errMsg)
        }
      }
    })
  },
  onShow() {
    let that = this
    let session_key = wx.getStorageSync('session_key')
    //console.log(session_key)
    if (session_key) {
      wx.checkSession({
        success: function(res) {
          //console.log(res)
          //同步微信运动
          //console.log(session_key)
          that.getWeRunData()
        },
        fail: function(res) {
          // console.log(res)
          that.wxLogin()
          //console.log(session_key)
        },
        complete: function(res) {
          //console.log(res)
        },
      })
    } else {
      console.log('need login')
      that.wxLogin()
    }

  },
  box() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getBoxStone',

      data: {
        aid: that._data.aid,
        openid: wx.getStorageSync('openid'),
        teamid: that.data.myteam.currentteamid
      },
      method: 'POST',
      success: (res) => {
        console.log(res.data)
        that.setData({
          hideBox: false,
          stones: res.data
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
  viewStone: function(e) {
    let id = e.currentTarget.id
    let stone = this.data.stones[id]
    //console.log(stone)
    this.setData({
      hideStone: false,
      stoneSelected: stone
    })
  },
  useStone: function(e) {
    this.setData({
      hideUseStone: false
    })
  },
  getSelectedTeam(e) {
    let id = e.detail.value[0]

    let teamid = this.data.teams[id].displayorder
    console.log(teamid)
    this.setData({
      selectedteam: teamid
    })

  },
  cancelUseStone() {
    let that = this
    this.setData({
      hideUseStone: true,
      teamvalue: [0],
      selectedteam: 1,
      landIdhold: '',
      landIdexchange1: '',
      landIdexchange2: '',
    })

  },

  //使用宝石
  confirmUseStone() {

    let that = this
    var teamid, stoneid, data, postallowed
    // teamid = that.data.selectedteam
    postallowed = true
    stoneid = this.data.stoneSelected.id
    if (stoneid == 4) {
      teamid = that.data.selectedteam
      console.log(teamid)
      data = {
        aid: that._data.aid,
        stoneid: stoneid,
        teamid: that.data.teams_nomyteam[teamid - 1].displayorder,
        teamname: that.data.teams_nomyteam[teamid - 1].name,
        myteamid: that.data.myteam.displayorder,
        myteamname: that.data.myteam.name,
      }
      // console.log(data)
      // postallowed = false
      // return false
    } else if (stoneid == 2) {
      data = {
        aid: that._data.aid,
        stoneid: stoneid,
        myteamid: that.data.myteam.displayorder,
        myteamname: that.data.myteam.name,
      }
    } else if (stoneid == 6) {
      data = {
        aid: that._data.aid,
        stoneid: stoneid,
        myteamid: that.data.myteam.displayorder,
        myteamname: that.data.myteam.name,
      }
    } else if (stoneid == 5) {
      data = {
        aid: that._data.aid,
        stoneid: stoneid,
        myteamid: that.data.myteam.displayorder,
        myteamname: that.data.myteam.name,
      }
    } else if (stoneid == 1) {
      let landid = that.data.landIdhold

      if (landid == '' || isNaN(parseInt(landid)) || parseInt(landid) <= 0 || parseInt(landid) > 50 || that.data.lands[landid - 1].ptype != 0) {
        wx.showToast({
          title: '请输入正确的编号，只有普通地块才可以被抢夺',
          icon: 'none'
        })
        postallowed = false
        return false
      } else {
        data = {
          aid: that._data.aid,
          stoneid: stoneid,
          myteamid: that.data.myteam.displayorder,
          myteamname: that.data.myteam.name,
          landid: landid,
          taskid: that.data.lands[landid - 1].taskid,
          teamid: that.data.lands[landid - 1].owner[0]
        }
      }
    } else if (stoneid == 7) {
      let landid = that.data.landIdhold

      if (landid == '' || isNaN(parseInt(landid)) || parseInt(landid) <= 0 || parseInt(landid) > 50 || that.data.lands[landid - 1].ptype != 0) {
        wx.showToast({
          title: '风暴宝石只能用于普通地块，请输入正确编号',
          icon: 'none'
        })
        postallowed = false
        return false
      } else {
        data = {
          aid: that._data.aid,
          stoneid: stoneid,
          myteamid: that.data.myteam.displayorder,
          myteamname: that.data.myteam.name,
          landid: landid,
          taskid: that.data.lands[landid - 1].taskid,
          teamid: that.data.lands[landid - 1].owner[0] ? that.data.lands[landid - 1].owner[0] : 0
        }
        console.log(data)
      }
    } else if (stoneid == 3) {
      let landIdexchange1 = that.data.landIdexchange1
      let landIdexchange2 = that.data.landIdexchange2
      // console.log(landid)
      if (landIdexchange1 == '' || isNaN(parseInt(landIdexchange1)) || parseInt(landIdexchange1) <= 0 || parseInt(landIdexchange1) > 50 || that.data.lands[landIdexchange1 - 1].ptype != 0 || landIdexchange2 == '' || isNaN(parseInt(landIdexchange2)) || parseInt(landIdexchange2) <= 0 || parseInt(landIdexchange2) > 50 || that.data.lands[landIdexchange2 - 1].ptype != 0) {
        wx.showToast({
          title: '请输入正确的编号，只有普通地块才可以被交换',
          icon: 'none'
        })
        postallowed = false
        return false
      } else {
        console.log(that.data.lands[landIdexchange2 - 1].owner.length)
        data = {
          aid: that._data.aid,
          stoneid: stoneid,
          landIdexchange1: landIdexchange1,
          landIdexchange2: landIdexchange2,
          myteamid: that.data.myteam.displayorder,
          myteamname: that.data.myteam.name,
          taskid1: that.data.lands[landIdexchange1 - 1].taskid,
          teamid1: that.data.lands[landIdexchange1 - 1].owner.length > 0 ? that.data.lands[landIdexchange1 - 1].owner[0] : '0',
          taskid2: that.data.lands[landIdexchange2 - 1].taskid,
          teamid2: that.data.lands[landIdexchange2 - 1].owner.length > 0 ? that.data.lands[landIdexchange2 - 1].owner[0] : '0'
        }
      }
    }
    if (postallowed) {
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
          return
        }

      }
      wx.request({
        url: app.globalData.config.apiUrl + 'index.php?act=useStone',

        data: data,
        method: 'POST',
        success: (res) => {
          console.log(res.data)
          let data = res.data
          if (data.status) {
            that.setData({
              hideBox: true,
              hideStone: true,
              hideUseStone: true,
              teamvalue: [0],
              selectedteam: 1,
              landIdhold: '',
              landIdexchange1: '',
              landIdexchange2: '',
            })
            wx.showToast({
              title: data.msg,
              icon: 'none',
              success() {
                setTimeout(() => {
                  that.fetch()
                }, 2000)
              }
            })
          } else {
            wx.showToast({
              title: data.msg,
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
  },
  hideStone() {
    this.setData({
      hideStone: true
    })
  },
  hideBox() {
    this.setData({
      hideBox: true
    })
  },
  updatelandIdhold(e) {
    //console.log(e.detail.value)
    this.setData({
      landIdhold: e.detail.value
    })
  },

  updatelandIdexchange1(e) {
    // console.log(e.detail.value)
    this.setData({
      landIdexchange1: e.detail.value
    })
  },
  updatelandIdexchange2(e) {
    //console.log(e.detail.value)
    this.setData({
      landIdexchange2: e.detail.value
    })
  },
  onPullDownRefresh() {
    let that = this
    wx.showNavigationBarLoading();
    clearInterval(that.data.timer)
    that.setData({
      timer: '',
      showTime: false
    })
    this.fetch();

    wx.hideNavigationBarLoading();
    wx.stopPullDownRefresh()
  },
  onShareAppMessage: function(ops) {
    let that = this
    //console.log(ops)

    if (ops.from === 'button') {
      // 来自页面内转发按钮
      //console.log(ops.target)
    }
    return {
      title: that._data.title,
      path: 'pages/game/splash?aid=' + that._data.aid,
      imageUrl: that.data.act.sharepic ? app.globalData.config.apiUrl + 'sharepic/' + that.data.act.sharepic : app.globalData.config.apiUrl + 'sharepic/1.jpg',
      success: function(res) {

      },
      fail: function(res) {

      }
    }
  },
})