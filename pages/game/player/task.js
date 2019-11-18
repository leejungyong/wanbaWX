var roleid;
const app = getApp()
Page({
  _data: {
    task: null,
    teamid: 0,
    teamname: '',
    act: null,
    role:null
    },

  data: {
    actMode:null,
    task: null,
    flag: true,
    inputTxt: '',
    answer: '',
    stoneSelected: null,
    hideStone:true,
    btnTxt: '播放音频',
    imgUrl:app.globalData.config.imgUrl,
    uploadUrl:app.globalData.config.uploadUrl,
    slogan: '让世界更好玩'
  },
  onUnload() {
    if (this.data.task.media==1) {
      this.innerAudioContext.stop()
    }
  },
  registerAudioContext: function (src) {
    //console.log('ok')
    this.innerAudioContext = wx.createInnerAudioContext();
    this.innerAudioContext.src = src
    this.innerAudioContext.onEnded(
      (res) => {
        this.setData({
          btnTxt: '播放音频'
        })
      })
    this.innerAudioContext.onError((res) => {
      console.log('播放音频失败' + res);
    })
    this.innerAudioContext.onStop((res) => {
      console.log('播放结束!');
    })
  },
  hideStone(){
      this.setData({
        hideStone:true
      })
  },
  preview(e) {
    let id = e.currentTarget.id
    let url =  this.data.task.pics[id].url
    let urls = []
    urls.push(url)
    wx.previewImage({
      urls: urls
    })
  },
  preventTouchMove: function() {

  },
  updateAnswer(e) {
    this.setData({
      answer: e.detail.value
    })
  },
  checkAnswer() {
    let that = this
    if (this.data.answer == '') {
      wx.showToast({
        title: '请输入答案',
        icon: 'none'
      })
      return
    } else {
      let answer = this.data.answer
      let defaultAnswer = this._data.task.answer
      if (this.data.task.qtype == 1) {
        if (answer == defaultAnswer) {
          wx.showLoading({
            title: '提交中',
            mask: true
          })
          //that.disabled = true
          wx.request({
            url: app.globalData.config.apiUrl+'index.php?act=autoUpdateScore',
            data: {
              taskid: this._data.task.taskid,
              aid: this._data.task.aid,
              teamid: this._data.teamid,
              owner: this._data.task.owner.join(','),
              ptype: this._data.task.ptype,
              pvalue: this._data.task.pvalue,
              mine: this._data.task.mine,
              displayorder: this._data.task.displayorder,
              pass: 2
            },
            method: 'POST',
            success: function(res) {
              //that.disabled = false
              wx.hideLoading()
              console.log(res.data)
              let data = res.data
              //console.log(data)
              wx.showToast({
                title: '回答正确,' + data,
                icon: 'none'
              })
              setTimeout(() => {
                wx.reLaunch({
                  url: './main?aid=' + that._data.task.aid,
                })
              }, 2000)

            },
            fail: (res) => {
              //that.disabled = false
              wx.hideLoading()
              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }
          })



        } else {
          wx.showToast({
            title: '啊哦，回答错误哦',
            icon: 'none'
          })
          setTimeout(() => {
            wx.navigateBack()
          }, 2000)

        }
      } else if (this.data.task.qtype == 2) {
        let answerArr = defaultAnswer.split('|')
        let result = true
        for (let i in answerArr) {
          if (answer.indexOf(answerArr[i]) == -1) {
            result = false
            break
          }
        }
        if (result) {
          //that.disabled = true
          wx.showLoading({
            title: '提交中',
            mask: true
          })
          wx.request({
            url: app.globalData.config.apiUrl+'index.php?act=autoUpdateScore',
            data: {
              taskid: this._data.task.taskid,
              aid: this._data.task.aid,
              teamid: this._data.teamid,
              owner: this._data.task.owner.join(','),
              ptype: this._data.task.ptype,
              pvalue: this._data.task.pvalue,
              mine: this._data.task.mine,
              displayorder: this._data.task.displayorder,
              pass: 2
            },
            method: 'POST',
            success: function(res) {
              // that.disabled = false
              wx.hideLoading()
              //console.log(res)
              let data = res.data
              //console.log(data)
              wx.showToast({
                title: '回答正确,' + data,
                icon: 'none'
              })
              setTimeout(() => {
                wx.reLaunch({
                  url: './main?aid=' + that._data.task.aid,
                })
              }, 2000)

            },
            fail: (res) => {
              //that.disabled = false
              wx.hideLoading()
              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }
          })
        } else {
          wx.showToast({
            title: '啊哦，回答错误哦',
            icon: 'none'
          })
          setTimeout(() => {
            wx.navigateBack()
          }, 2000)
        }
      } else if (this.data.task.qtype == 3) {
        if (answer.indexOf(defaultAnswer) >= 0) {
          wx.showLoading({
            title: '提交中',
            mask: true
          })
          //that.disabled = true
          wx.request({
            url: app.globalData.config.apiUrl+'index.php?act=autoUpdateScore',
            data: {
              taskid: this._data.task.taskid,
              aid: this._data.task.aid,
              teamid: this._data.teamid,
              owner: this._data.task.owner.join(','),
              ptype: this._data.task.ptype,
              pvalue: this._data.task.pvalue,
              mine: this._data.task.mine,
              displayorder: this._data.task.displayorder,
              pass: 2
            },
            method: 'POST',
            success: function(res) {
              //that.disabled = false
              wx.hideLoading()
              //console.log(res)
              let data = res.data
              //console.log(data)
              wx.showToast({
                title: '回答正确,' + data,
                icon: 'none'
              })
              setTimeout(() => {
                wx.reLaunch({
                  url: './main?aid=' + that._data.task.aid,
                })
              }, 2000)

            },
            fail: (res) => {
              //that.disabled = false
              wx.hideLoading()
              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }
          })
        } else {
          wx.showToast({
            title: '啊哦，回答错误哦',
            icon: 'none'
          })
          setTimeout(() => {
            wx.navigateBack()
          }, 2000)
        }
      } else if (this.data.task.qtype == 4) {
        if (defaultAnswer.indexOf(answer) >= 0) {
          wx.showLoading({
            title: '提交中',
            mask: true
          })
          //that.disabled = true
          wx.request({
            url: app.globalData.config.apiUrl+'index.php?act=autoUpdateScore',
            data: {
              taskid: this._data.task.taskid,
              aid: this._data.task.aid,
              teamid: this._data.teamid,
              owner: this._data.task.owner.join(','),
              ptype: this._data.task.ptype,
              pvalue: this._data.task.pvalue,
              mine: this._data.task.mine,
              displayorder: this._data.task.displayorder,
              pass: 2
            },
            method: 'POST',
            success: function(res) {
              //that.disabled = false
              wx.hideLoading()
             // console.log(res)
              let data = res.data
              //console.log(data)
              wx.showToast({
                title: '回答正确,' + data,
                icon: 'none'
              })
              setTimeout(() => {
                wx.reLaunch({
                  url: './main?aid=' + that._data.task.aid,
                })
              }, 2000)

            },
            fail: (res) => {
              //that.disabled = false
              wx.hideLoading()
              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }
          })
        } else {
          wx.showToast({
            title: '啊哦，回答错误哦',
            icon: 'none'
          })
          setTimeout(() => {
            wx.navigateBack()
          }, 2000)
        }
      }else if(this.data.task.qtype==6){
        let answerArr = defaultAnswer.split('|')
        let n=0
        for (let i in answerArr) {
          if (answer.indexOf(answerArr[i]) > -1) {
            n++
            
          }
        }
        if (n >= 3){
          wx.showLoading({
            title: '提交中',
            mask: true
          })
          wx.request({
            url: app.globalData.config.apiUrl+'index.php?act=autoUpdateScore',
            data: {
              taskid: this._data.task.taskid,
              aid: this._data.task.aid,
              teamid: this._data.teamid,
              owner: this._data.task.owner.join(','),
              ptype: this._data.task.ptype,
              pvalue: this._data.task.pvalue,
              mine: this._data.task.mine,
              displayorder: this._data.task.displayorder,
              pass: 2
            },
            method: 'POST',
            success: function (res) {
              // that.disabled = false
              wx.hideLoading()
              //console.log(res)
              let data = res.data
              //console.log(data)
              wx.showToast({
                title: '回答正确,' + data,
                icon: 'none'
              })
              setTimeout(() => {
                wx.reLaunch({
                  url: './main?aid=' + that._data.task.aid,
                })
              }, 2000)

            },
            fail: (res) => {
              //that.disabled = false
              wx.hideLoading()
              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }
          })

        }else{
          wx.showToast({
            title: '啊哦，回答错误哦',
            icon: 'none'
          })
          setTimeout(() => {
            wx.navigateBack()
          }, 2000)
        }
      }
    }
  },
  updateTxt(e) {
    this.setData({
      inputTxt: e.detail.value
    })
  },
  challenge() {
    let ops = JSON.stringify(this._data.task)

    if (this._data.task.qtype == 5 || this._data.task.qtype == 7) {
      let op = this._data.task
      console.log(op)
      op.posid = op.displayorder
      op.openid = wx.getStorageSync('openid')
      op.teamid = this._data.teamid
      op.teamname = this._data.teamname
      op.act = this._data.task.qtype == 5 ? 'checktask' :'addMoney'

      wx.navigateTo({
        url: './showtaskcode?ops=' + JSON.stringify(op)
      })
    } else {
      wx.navigateTo({
        url: './challenge?ops=' + ops + '&teamid=' + this._data.teamid,
      })
    }
  },
  mine() {
    this.setData({
      flag: false
    })
  },
  hide() {
    this.setData({
      flag: true
    })
  },
  viewBox(){

  },
  updateMine() {
    //console.log(this.data.inputTxt)
    if (this.data.inputTxt == '' || parseInt(this.data.inputTxt) <= 0 || isNaN(parseInt(this.data.inputTxt))) {
      wx.showToast({
        title: '请填写金额',
        icon: 'none'
      })
      return
    }
    let that = this
    let mine = that.data.inputTxt
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=updateMine',
      data: {
        taskid: that._data.task.taskid,
        mine: mine,
        aid: that._data.task.aid,
        pvalue: that._data.task.pvalue,
        teamid: that._data.teamid
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        //console.log(data)
        if (data.status) {
          wx.showToast({
            title: data.msg
          })
          setTimeout(() => {
            wx.redirectTo({
              url: './main?aid=' + that._data.task.aid,
            })
          }, 2000)

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
  },
 
  play() {

    if (this.data.btnTxt == '播放音频' || this.data.btnTxt == '继续播放') {

      this.innerAudioContext.play()
      this.setData({
        btnTxt: '暂停播放'
      })
    } else {

      this.innerAudioContext.pause()
      this.setData({
        btnTxt: '继续播放'
      })
    }

  },
  captainTask(options){
    let that = this
    console.log(options)
    
    let task = JSON.parse(options.ops)
    console.log(task)

    let act = JSON.parse(options.act)
    this._data.act = act
    //console.log(task)
    let teamid = options.teamid
    //console.log(teamid)
    this._data.teamid = teamid
    let teamname = options.teamname
    //console.log(teamid)
    this._data.teamname = teamname





    this._data.teamname = options.teamname
    this._data.task = task
    //let sessionname = 'aid' + task.aid + '_task' + task.displayorder + '_team' + teamid
    

   //let session = wx.getStorageSync(sessionname) ? wx.getStorageSync(sessionname) : 0
    
    wx.setNavigationBarTitle({
      title: task.displayorder + '号点-' + task.name
    })
    wx.showLoading({
      title: '加载中',
    })
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=queryAnswerstatus',
      data: {
        aid: task.aid,
        teamid: this._data.teamid,
        taskid: task.taskid,
        openid: wx.getStorageSync('openid'),
        sessionname: '',
        sessionvalue: ''
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        let status = data.pass.pass ? data.pass.pass : -1
       
        this.setData({
          task: {
            'name': task.name,
            'memo': task.memo,
            'pics': task.pics,
            'qtype': task.qtype,
            'ptype': task.ptype,
            'mine': task.mine,
            'owner': task.owner.join(','),
            'teamid': teamid,
            'media': task.media,
            'answer': task.answer,
            'displayorder':task.displayorder,
            'url': task.url,
            'tip1': task.tip1 ? task.tip1:null,
            'tip2': task.tip2 ? task.tip2 : null
          },
          answerStatus: status,
          roleid: roleid,
          actMode: that._data.act.mode

        })
         console.log(this.data)
        wx.hideLoading()
        if (task.media == 1) {
          this.registerAudioContext(task.url);
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
  onLoad: function(options) {
    let that=this
    let ops = options
    console.log(ops)
    roleid = ops.roleid
    let act=JSON.parse(ops.act)
    let actid=act.aid
    let task = JSON.parse(ops.ops)
    let taskid=task.taskid
  
   wx.request({
     url: app.globalData.config.apiUrl + 'index.php?act=actInfo',
     data:{
       aid:actid,
       openid: wx.getStorageSync('openid')
     },
     method:'POST',
     success(res){
       console.log(res.data)
       that.setData({
         slogan:res.data.act.slogan||'让世界更好玩'
       })
     }
   })
    if(roleid==1){
      wx.request({
        url: app.globalData.config.apiUrl + 'index.php?act=getRedbagTodo',
        data: {
          aid: actid,
          openid: wx.getStorageSync('openid')
        },
        method: 'POST',
        success: (res) => {
          let data = res.data
          console.log(data)
          if (data && data.status == 0 && taskid==data.taskid) {
            
          }else{

            that.captainTask(options)
          }
        },
        fail: (err) => {
          wx.showToast({
            title: '网络错误',
            icon: 'none'
          })
        }
      })

    } else if (roleid == 0){
      this.captainTask(options)
    }

  },

})