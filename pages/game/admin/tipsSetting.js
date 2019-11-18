var aid = null
const app = getApp()
var arr = [], event
Page({


  data: {
    tip: '',
    tipsList: []
  },

 
  updateTip(e) {
    event=e
   this.setData({
      tip: e.detail.value
    })
  },

 
  addTip() {
    let that = this
    let tip = that.data.tip
    if(tip==''){
      wx.showToast({
        title: '请输入相关提示语',
        icon: 'none'
      })
      return
    }
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=addAITip',
      data: {
        aid: aid,
        tip: tip
      },
      method: 'POST',
      success: (res) => {
        let data=res.data
        if(data.status){
          arr.push(data.item)
          that.setData({
            tipsList: arr.map((it)=>{
              return it.tip
            }),
            tip: ''
          })
        }
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
     
    // console.log(that.data.tipsList)
  },

 
  deleteTip(e) {
    let that=this
    let index = e.currentTarget.dataset.idx
    wx.showModal({
      title: '警告：',
      content: '确定要删除吗？',
      success:(res)=>{
        if(res.confirm){
          wx.request({
            url: app.globalData.config.apiUrl + 'index.php?act=delAITip',
            data: {
              aid: aid,
              tipid: arr[index].id
            },
            method: 'POST',
            success: (res) => {
              let data = res.data
              console.log(data)
              if (data.status) {
                arr.splice(index, 1)
                let tipArr = arr.map((it) => {
                  return it.tip
                })
                that.setData({
                  tipsList: tipArr
                })
              }
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
        }
      }
    })
   

  
  },




  fetch(){
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getAITips',
      data: {
        aid: aid
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        arr=data
        this.setData({
          tipsList:data.map((item)=>{
             return item.tip
          })
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
  onLoad: function (options) {
    aid = options.aid
    this.fetch()
    // arr = coach.split(' ')
    // console.log(coach)
    // for (let i = 0; i < arr.length; i++) {
    //   arr[i].trim()
    //   if (arr[i] == '') {
    //     arr.splice(i, 1)
    //   }
    // }
    // console.log(arr)
    // this.setData({
    //  tipsList: arr
    // })
    // console.log(this.data.tipsList)
  }

})