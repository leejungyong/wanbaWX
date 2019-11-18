var aid=null
const app = getApp()
Page({

  data: {
  nick:'',
  list:null
  },
  confrim(e){
    let id = e.currentTarget.id
    let openid = this.data.list[id].openid
    let nick = this.data.list[id].nick
    let that = this
    wx.showModal({
      title: '提示',
      content: '确定要转让管理员给'+nick+'吗？',
      success(res){
          if(res.confirm){
            
            wx.request({
              url: app.globalData.config.apiUrl+'index.php?act=shiftManager',
              data: {
                aid: aid,
                openid: openid
              },
              method: 'POST',
              success: (res) => {
                let data = res.data
               // console.log(data)
                if(data.status){
                  wx.showToast({
                    title: data.msg,
                  })
                  setTimeout(() => {
                    wx.redirectTo({
                      url: '../player/main?aid='+aid,
                    })
                  }, 2000)
                }
                else{
                  wx.showToast({
                    title: '操作失败',
                    icon:'none'
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
          }
      }
    })
  },
  searchNick(){
    //console.log(this.data.nick)
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=searchNick',
      data: {
        nick: that.data.nick
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
       if(data.length==0){
          wx.showToast({
            title: '未找到匹配项',
            icon:'none'
          })
         that.setData({
           list: null
         })
       }else{
          that.setData({
            list:data
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

  updateNick: function (e){
    this.setData({
      nick:e.detail.value
    })
  },
  onLoad: function (options) {
    aid=options.aid
  },

})