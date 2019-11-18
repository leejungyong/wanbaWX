
var act, aid, mid, teamid, taskid, token, openid, sellprice, posid, money, buyer, seller_teamname, buyer_teamname = null
const app = getApp()
Page({

  _data:{

  },
  data: {

  },

  onLoad: function (options) {

  },

  scan() {
    let that = this
    wx.scanCode({
      onlyFromCamera: false,
      success: (res) => {
        let result = res.result
        //console.log(result)

        act = result.split('&')[0].split('=')[1]
        switch (act){
          case 'isCaptain':
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
                      //console.log(res)
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
          break;
          case 'auction':
            act = result.split('&')[0].split('=')[1]
            teamid = result.split('&')[2].split('=')[1]
            token = result.split('&')[3].split('=')[1]
            aid = result.split('&')[4].split('=')[1]
            taskid = result.split('&')[5].split('=')[1]
            sellprice = result.split('&')[6].split('=')[1]
            posid = result.split('&')[7].split('=')[1]
            seller_teamname = result.split('&')[8].split('=')[1]

            wx.showModal({
              title: '判定',
              content: seller_teamname + '出价' + sellprice +'竞拍'+posid+'号地产，确认通过吗？',
              success: (res) => {
                if (res.confirm) {
                  wx.request({
                    url: app.globalData.config.apiUrl+'index.php?act=dealAuction',
                    data: {
                      aid: aid,
                      teamid: teamid,
                      token: token,
                      taskid: taskid,
                      sellprice: sellprice,
                      posid: posid,
                      seller_teamname: seller_teamname
                    },
                    method: 'POST',
                    success: (res) => {
                     //console.log(res.data)
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
            break;
        }
      
      
        
       


      },
      fail:(res)=>{
        console.log(res)
      }
    })
  }
})