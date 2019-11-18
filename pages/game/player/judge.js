var act, aid,  teamid, taskid, token, openid, sellprice,posid, money,teamname,pass= null
const app = getApp()
Page({

  _data: {

  },
  data: {
  flag:true,
  teamname:'',
  inputTxt:''
  },

  onLoad: function(options) {

  },
  updateTxt(e) {
    this.setData({
      inputTxt: e.detail.value
    })
  },
  cancelAddMoney() {
    this.setData({
      flag: true
    })
  },
  confirmAddMoney() {

    if (this.data.inputTxt == '' || isNaN(parseInt(this.data.inputTxt)) ) {
      wx.showToast({
        title: '请输入金额',
        icon: 'none'
      })
    } else {
      let ops = {
        act: 'addMoney',
        teamid: teamid,
        aid: aid,
        posid:posid>0 ? posid:0,
        taskid: taskid > 0 ? taskid : 0,
        openid: openid,
        token: token,
        score: parseInt(this.data.inputTxt),
        teamname: teamname
      }
      let that=this
      wx.request({
        url: app.globalData.config.apiUrl+'index.php?act=addMoney',
        data:ops,
        method: 'POST',
        success:(res)=>{
          console.log(res.data)
           let data=res.data
           if(data.status){
             wx.showToast({
               title: data.msg,
               icon: 'none'
             })
             that.setData({
               flag:true,
               teamname: '',
               inputTxt: ''
             })
           }else{
             wx.showToast({
               title: data.msg,
               icon: 'none'
             })
           }
        },
        fail:(res)=>{
          wx.showToast({
            title: '网络错误',
            icon: 'none'
          })
        }
      })
    }
  },
  scan() {
    let that = this
    wx.scanCode({
      onlyFromCamera: false,
      success: (res) => {
        let result = res.result
        //console.log(result)

        act = result.split('&')[0].split('=')[1]

        if (act == 'isCaptain') {
          openid = result.split('&')[1].split('=')[1]
          teamid = result.split('&')[2].split('=')[1]

          token = result.split('&')[3].split('=')[1]
          aid = result.split('&')[4].split('=')[1]

          wx.showModal({
            title: '判定',
            content: '确定要使其成为队长吗？',
            success: (res) => {
              if (res.confirm) {
                wx.request({
                  url: app.globalData.config.apiUrl+'index.php?act=iscaptain',
                  data: {
                    aid: aid,
                    teamid: teamid,
                    openid: openid
                  },
                  method: 'POST',
                  success: (res) => {
                   // console.log(res)
                    let data = res.data
                    if (data.status) {
                      wx.showToast({
                        title: '操作成功'
                      })
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

              } else {


              }
            }
          })
        }
        else if(act=='checktask'){
          openid = result.split('&')[1].split('=')[1]
          teamid = result.split('&')[2].split('=')[1]
          token = result.split('&')[3].split('=')[1]
          aid = result.split('&')[4].split('=')[1]
          taskid = result.split('&')[5].split('=')[1]
          posid = result.split('&')[7].split('=')[1]
          teamname = result.split('&')[8].split('=')[1]

          wx.showModal({
            title: '判定',
            content: '你确定' + teamname+'在'+posid+'号点的挑战成功吗',
            cancelText:'否',
            confirmText:'是',
            success: (res) => {
              if (res.confirm) {
                 pass=0 

              } else {
                 pass=-2
                
              }
              wx.request({
                url: app.globalData.config.apiUrl+'index.php?act=coachCheckTask',
                data: {
                  openid: wx.getStorageSync('openid'),
                  aid: aid,
                  teamid: teamid,
                  token: token,
                  taskid: taskid,
                  posid: posid,
                  teamname: teamname,
                  pass: pass
                },
                method: 'POST',
                success: function (res) {
                  //console.log(res.data)
                  let data = res.data
                  if (data.status) {
                    wx.showToast({
                      title: '操作成功'
                    })
                  } else {
                    wx.showToast({
                      title: data.msg,
                      icon: 'none'
                    })
                  }
                },
                fail: function (res) {
                  wx.showToast({
                    title: '网络错误',
                    icon: 'none'
                  })
                }
              })
            }
          })
          
        }
        else if (act == 'addMoney') {
          openid = result.split('&')[1].split('=')[1]
          teamid = result.split('&')[2].split('=')[1]
          token = result.split('&')[3].split('=')[1]
          aid = result.split('&')[4].split('=')[1]
          taskid = result.split('&')[5].split('=')[1]
          posid = result.split('&')[7].split('=')[1]
          teamname = result.split('&')[8].split('=')[1]
          that.setData({
            flag:false,
            teamname:teamname
          })
       
        }


      }
    })
  }
})