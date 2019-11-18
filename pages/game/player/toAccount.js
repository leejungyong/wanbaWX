var audioCtx
var arr = [], //暂存这次选择的点
  alreadyArr = [], //存放已经选择过得点
  winsFive = [], //所有五子连线赢法
  winsThree = [], //所有3子连线赢法
  winsSeven = [], //所有7子连线赢法
  countFive, //总数
  countThree,
  countSeven,
  content='',
  over = false,
  app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    audio: 'https://img.wondfun.com/wanba/api/audio/clear.mp3',
    lands: null,
    teams: null,
    showarr: [],
    aid: null,
    act_status: null,
    teamid: null,
    myteam: null
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    let that = this
    let aid = options.aid,
      teamid = options.myteamid


    // let teamid = this.data.myteam.currentteamid
    // if (alreadyArr.length > 0) {
    //   console.log(alreadyArr)
    //   alreadyArr.map((item, index) => {
    //     lands[item - 1].checked = 1
    //   })
    // }
    that.setData({
      showarr: alreadyArr,
      aid: aid,
      teamid: teamid
    })
    that.fetch()

    this.fiveLine()
    this.threeLine()
    this.sevenLine()


  },

  fetch() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=actInfo',
      method: 'POST',
      data: {
        aid: that.data.aid,
        teamid: that.data.teamid,
        openid: wx.getStorageSync('openid')
      },
      success: res => {

        let tasks = res.data.task
        for (let i in tasks) {
          let owners = tasks[i].owner ? tasks[i].owner.split(',') : []
          tasks[i].owner = owners
        }
        let lands = tasks.map((item, index) => {
          item.checked = 0
          return item
        })
        // console.log(res.data)
        that.setData({
          lands: lands,
          teams: res.data.teams,
          myteam: res.data.myteam,
          act_status: res.data.act.status
        })
        console.log(that.data)
      }
    })
  },
  /**
   * 点击每个点位时的操作
   */
  choose(e) {
    let id = e.currentTarget.id,
      teamid = this.data.teamid,
      act_status = this.data.act_status,
      owner = this.data.lands[id].owner.length == 1 ? this.data.lands[id].owner[0] : null,
      landsS = this.data.lands
    console.log(alreadyArr)
    console.log(id)
    console.log(alreadyArr.indexOf(parseInt(id) + 1))
    if (parseInt(teamid) == parseInt(owner)) {
      //游戏过程阶段连线
      if (act_status == 0) {
        if (this.data.lands[id].ptype != 0) {
          wx.showToast({
            title: '该点位不属于你，请选择自己拥有的点位',
            icon: 'none'
          })
        } else {
          if (alreadyArr.indexOf(parseInt(id) + 1) == -1) {
            let index = arr.indexOf(parseInt(id) + 1)
            if (index == -1) {
              arr.push(parseInt(id) + 1)
              console.log(arr)
              landsS[id].checked = 1
              this.setData({
                lands: landsS
              })
            } else {
              arr.splice(index, 1)
              console.log(arr)
              landsS[id].checked = 0
              this.setData({
                lands: landsS
              })
            }
          } else {

          }
        }
      } else if (act_status == 5) {
        if (this.data.lands[id].ptype > 0) {
          if (this.data.lands[id].owner.length == 1 && (parseInt(this.data.lands[id].isSold) > 0 || this.data.lands[id].ptype == 2) && this.data.lands[id].owner[0] == this.data.myteam.currentteamid) {
            if (alreadyArr.indexOf(parseInt(id) + 1) == -1) {
              let index = arr.indexOf(parseInt(id) + 1)
              if (index == -1) {
                arr.push(parseInt(id) + 1)
                console.log(arr)
                landsS[id].checked = 1
                this.setData({
                  lands: landsS
                })
              } else {
                arr.splice(index, 1)
                console.log(arr)
                landsS[id].checked = 0
                this.setData({
                  lands: landsS
                })
              }
            }

          } else {
            wx.showToast({
              title: '该点位不属于你，请选择自己拥有的点位',
              icon: 'none'
            })
          }
        } else {
          if (alreadyArr.indexOf(parseInt(id) + 1) == -1) {
            let index = arr.indexOf(parseInt(id) + 1)
            if (index == -1) {
              arr.push(parseInt(id) + 1)
              console.log(arr)
              landsS[id].checked = 1
              this.setData({
                lands: landsS
              })
            } else {
              arr.splice(index, 1)
              console.log(arr)
              landsS[id].checked = 0
              this.setData({
                lands: landsS
              })
            }
          } else {

          }
        }
      }

    } else {
      wx.showToast({
        title: '该点位不属于你，请选择自己拥有的点位',
        icon: 'none'
      })
    }

  },
  /**
   * 五点连线赢法
   */
  fiveLine() {
    //赢法数组
    var wins = []
    for (var i = 0; i < 7; i++) {
      wins[i] = [];
      for (var j = 0; j < 7; j++) {
        wins[i][j] = [];
      }
    }
    var count = 0
    //横向赢法数组
    for (var i = 0; i < 7; i++) {
      for (var j = 0; j < 3; j++) {
        for (var k = 0; k < 5; k++) {
          wins[i][j + k][count] = true
        }
        count++
      }
    }

    //纵线赢法
    for (var i = 0; i < 7; i++) {
      for (var j = 0; j < 3; j++) {
        for (var k = 0; k < 5; k++) {
          wins[j + k][i][count] = true
        }
        count++
      }
    }

    //正斜线赢法
    for (var i = 0; i < 3; i++) {
      for (var j = 0; j < 3; j++) {
        for (var k = 0; k < 5; k++) {
          wins[i + k][j + k][count] = true
        }
        count++
      }
    }

    //反斜线赢法
    for (var i = 0; i < 3; i++) {
      for (var j = 6; j > 3; j--) {
        for (var k = 0; k < 5; k++) {
          wins[i + k][j - k][count] = true
        }
        count++
      }
    }
    countFive = count
    winsFive = wins
  },

  /**
   * 三点连线赢法计算
   */
  threeLine() {
    //赢法数组
    var wins = []
    for (var i = 0; i < 7; i++) {
      wins[i] = [];
      for (var j = 0; j < 7; j++) {
        wins[i][j] = [];
      }
    }
    var count = 0
    //横向赢法数组
    for (var i = 0; i < 7; i++) {
      for (var j = 0; j < 5; j++) {
        for (var k = 0; k < 3; k++) {
          wins[i][j + k][count] = true
        }
        count++
      }
    }

    // //纵线赢法
    for (var i = 0; i < 7; i++) {
      for (var j = 0; j < 5; j++) {
        for (var k = 0; k < 3; k++) {
          wins[j + k][i][count] = true
        }
        count++
      }
    }

    // //正斜线赢法
    for (var i = 0; i < 5; i++) {
      for (var j = 0; j < 5; j++) {
        for (var k = 0; k < 3; k++) {
          wins[i + k][j + k][count] = true
        }
        count++
      }
    }

    // //反斜线赢法
    for (var i = 0; i < 5; i++) {
      for (var j = 6; j > 1; j--) {
        for (var k = 0; k < 3; k++) {
          wins[i + k][j - k][count] = true
        }
        count++
      }
    }
    // console.log(count)
    countThree = count
    winsThree = wins
  },
  /**
   * 七点连线赢法计算
   */
  sevenLine() {
    //赢法数组
    var wins = []
    for (var i = 0; i < 7; i++) {
      wins[i] = [];
      for (var j = 0; j < 7; j++) {
        wins[i][j] = [];
      }
    }
    var count = 0
    //横向赢法数组
    for (var i = 0; i < 7; i++) {
      for (var j = 0; j < 1; j++) {
        for (var k = 0; k < 7; k++) {
          wins[i][j + k][count] = true
        }
        count++
      }
    }

    // //纵线赢法
    for (var i = 0; i < 7; i++) {
      for (var j = 0; j < 1; j++) {
        for (var k = 0; k < 7; k++) {
          wins[j + k][i][count] = true
        }
        count++
      }
    }

    // //正斜线赢法
    for (var i = 0; i < 1; i++) {
      for (var j = 0; j < 1; j++) {
        for (var k = 0; k < 7; k++) {
          wins[i + k][j + k][count] = true
        }
        count++
      }
    }

    //反斜线赢法
    for (var i = 0; i < 1; i++) {
      for (var j = 6; j > 5; j--) {
        for (var k = 0; k < 7; k++) {
          wins[i + k][j - k][count] = true
        }
        count++
      }
    }
    // console.log(count)
    countSeven = count
    winsSeven = wins
  },
  /**
   * 点击确定连线按钮
   */

  confirm() {
    var
      flag = false,
      fiveWin = [],
      threeWin = [],
     sevenWin = []
      over = false
      

    let that = this
    
 
    //初始化数组
    for (var i = 0; i < countFive; i++) {
      fiveWin[i] = 0;
    }
    for (var i = 0; i < countThree; i++) {
      threeWin[i] = 0;
    }
    for (var i = 0; i < countSeven; i++) {
      sevenWin[i] = 0;
    }
    let len = arr.length
    console.log(len)
    //计算点位价值
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getLandsValue',
      method: 'POST',
      data: {
        aid: that.data.aid,
        teamid: that.data.teamid,
        arr: arr
      },
      success: (res) => {
        console.log(res)
        let dt = res.data
        let total = 0,
          pvalue1 = null,
          pvalue2 = null,
          pvalue3 = null,
          pvalue4 = null,
          pvalue5 = null,
          pvalue6 = null,
          pvalue7 = null
          
        if (dt.total > 0) {
          total = dt.total
          pvalue1 = dt.pvalue1 ? dt.pvalue1 : null
          pvalue2 = dt.pvalue2 ? dt.pvalue2 : null
          pvalue3 = dt.pvalue3 ? dt.pvalue3 : null
          pvalue4 = dt.pvalue4 ? dt.pvalue4 : null
          pvalue5 = dt.pvalue5 ? dt.pvalue5 : null
          pvalue6 = dt.pvalue6 ? dt.pvalue6 : null
          pvalue7 = dt.pvalue7 ? dt.pvalue7 : null
          if (len == 3) {
            content = '三点连线可获得2x(' + pvalue1 + '+' + pvalue2 + '+' + pvalue3 + ')=' + total + '加分';
          } else if (len == 5) {
            content = '五点连线可获得3x(' + pvalue1 + '+' + pvalue2 + '+' + pvalue3 + '+' + pvalue4 + '+' + pvalue5 + ')=' + total + '加分';
          } else if (len == 7) {
            content = '七点连线可获得5x(' + pvalue1 + '+' + pvalue2 + '+' + pvalue3 + '+' + pvalue4 + '+' + pvalue5 + '+' + pvalue6 + '+' + pvalue7 + ')=' + total + '加分';
          }
          console.log(content)
          for (i = 0; i < len; i++) {
            let x = Math.floor((arr[i] - 1) / 7),
              y = (arr[i] - 1) % 7
            if (len == 5) {
              for (var k = 0; k < countFive; k++) {
                if (winsFive[x][y][k]) { //某种赢的某子true

                  fiveWin[k]++; //离胜利又进一步
                  if (fiveWin[k] == 5) { //如果达到5就赢了
                    wx.showModal({
                      title: '确定要连线吗?',
                      content: content,
                      success(res) {
                        if (res.confirm) {
                          console.log('用户点击确定')
                          that.postPoi()
                          flag = true
                          alreadyArr = []
                         
                        } else if (res.cancel) {
                          console.log('用户点击取消')
                        }
                      }
                    })
                    flag = true
                    alreadyArr = []
                  }
                }
              }
            } else if (len == 3) {
              for (var k = 0; k < countThree; k++) {
                if (winsThree[x][y][k]) { //某种赢的某子true
                  // console.log(fiveWin[k])
                  threeWin[k]++; //离胜利又进一步
                  if (threeWin[k] == 3) { //如果达到3就赢了
                    wx.showModal({
                      title: '确定要连线吗?',
                      content: content,
                      success(res) {
                        if (res.confirm) {
                          console.log('用户点击确定')
                          that.postPoi()


                        } else if (res.cancel) {
                          console.log('用户点击取消')
                        }
                      }
                    })
                    flag = true
                    alreadyArr = []
                  }
                }
              }
            } else if (len == 7) {
              for (var k = 0; k < countSeven; k++) {
                if (winsSeven[x][y][k]) { //某种赢的某子true
                  // console.log(fiveWin[k])
                  sevenWin[k]++; //离胜利又进一步
                  if (sevenWin[k] == 7) { //如果达到5就赢了
                    wx.showModal({
                      title: '确定要连线吗?',
                      content: content,
                      success(res) {
                        if (res.confirm) {
                          console.log('用户点击确定')
                          that.postPoi()


                        } else if (res.cancel) {
                          console.log('用户点击取消')
                        }
                      }
                    })
                    flag = true
                    alreadyArr = []
                  }
                }
              }
            } else {
              flag = true
              wx.showToast({
                title: '不满足3点、5点或7点连线的条件！请重新选择！',
                icon: 'none'
              })
            }
          }
          if (!over && !flag) {
            console.log('不满足连线条件!')
            wx.showToast({
              title: '不满足连线条件!',
              icon: 'none'
            })
          }
          console.log(over)
          console.log(flag)
        }
      }
    })

    let la = this.data.lands
    if (len == 0) {
      wx.showToast({
        title: '请选择连线的点位!',
        icon: 'none'
      })
    }
    

  },
  /**
   * 点击确定后的post请求
   */
  postPoi() {
    let la = this.data.lands
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=linkLands',
      method: 'POST',
      data: {
        aid: that.data.aid,
        teamid: that.data.teamid,
        arr: arr
      },
      success: res => {
        console.log(res)

        if (res.data.status) {
          wx.showToast({
            title: res.data.msg,
            icon: 'none'
          })
          over = true;
          arr.map((item, index) => {
            la[item - 1].checked = 2
          })
          // alreadyArr = alreadyArr.concat(arr)
          alreadyArr = []
          arr = []
          // alreadyArr.push('\n')
          that.setData({
            showarr: alreadyArr,
            lands: la
          })
          that.fetch()

          that.fiveLine()
          that.threeLine()
          that.sevenLine()
          audioCtx.play()
        }

      }
    })
  },

  /**
   * 取消所有选择的点位
   */
  cancel() {

  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {
    audioCtx = wx.createAudioContext('myAudio')
    audioCtx.setSrc(this.data.audio)
    
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {
    let pages = getCurrentPages()
    let prepage = pages[pages.length - 2]
    prepage.fetch()
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {

  }
})