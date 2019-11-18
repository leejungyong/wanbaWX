var act, aid, mid, teamid, taskid, token, openid, sellprice, posid, money, buyer, seller_teamname, buyer_teamname = null
const app = getApp()
Page({

 
  data: {
    flag:true,
    sellprice:null,
    
  },


  onLoad: function (options) {
    aid=options.aid
  },
  fetch(aid){
   wx.request({
     url: app.globalData.config.apiUrl+'index.php?act=getMyteamMoney',
     data:{
       aid:aid,
       openid:wx.getStorageSync('openid')
     },
     method:'POST',
     success:function(res){
       //console.log(res.data)
       let data=res.data
       money=data
     },
     fail:function(res){
       wx.showToast({
         title: '网络错误',
         icon: 'none'
       })
     }
   })
  },
  cancelTrade() {
    this.hide()
  },
  confirmTrade() {
    let t = new Date().getTime();
    let cache = wx.getStorageSync('lastpost')
    if (cache) {
      let duration = t - cache
      if (duration < 3000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none'
        })
        return
      }

    }
    wx.setStorageSync('lastpost', t)
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=dealTrade',
      data: {
        aid: aid,
        seller: teamid,
        buyer:buyer,
        taskid:taskid,
        price: sellprice,
        posid:posid,
        seller_teamname:seller_teamname,
        buyer_teamname: buyer_teamname,
        token:token
      },
      method: 'POST',
      success: (res) => {
        //console.log(res.data)
        let data = res.data
        if (data.status) {
          wx.showToast({
            title: '恭喜您成功购得此地',
            icon:'none'
          })
          setTimeout(()=>{
           wx.navigateTo({
             url:'./main?aid='+aid
           })
          },2000)
        } else {
          wx.showToast({
            title: '不允许的非法操作',
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
  hide() {
    this.setData({
      flag: true,
      sellprice:null
    })
  },
  scan() {
    
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=getMyteamMoney',
      data: {
        aid:aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: function (res) {
        let data = res.data
        //console.log(data)
        money = data.money
        buyer=data.buyer
        buyer_teamname=data.buyer_teamname
        console.log(buyer_teamname)
        wx.scanCode({
          onlyFromCamera: false,
          success: (res) => {
            let result = res.result
            console.log(result)

            act = result.split('&')[0].split('=')[1]
            teamid = result.split('&')[2].split('=')[1]
            token = result.split('&')[3].split('=')[1]
            aid = result.split('&')[4].split('=')[1]
            taskid = result.split('&')[5].split('=')[1]
            sellprice = result.split('&')[6].split('=')[1]
            posid = result.split('&')[7].split('=')[1]
            seller_teamname = result.split('&')[8].split('=')[1]
            //console.log(money)
            if (parseInt(money) < parseInt(sellprice)) {
              wx.showToast({
                title: '您的钱不够哦',
                icon: 'none'
              })
              return
            }
            if (act == 'sell') {
              that.setData({
                flag: false,
                sellprice: sellprice,
                posid: posid
              })
            }

          }
        })
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