// pages/my/myline/cashOut.js
var app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    navbar:['申请','记录'],
    currentTab:0,
    bankCard:'',
    uploadUrl: app.globalData.config.uploadUrl,
    cardName:'',
    cardNum:'',
    rawcardno:'',
    num4:'',
    cashNum:'',
    name:'',
    recordlist:[],
    accountBalance:null,
    canWithdraw:0
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    console.log(options)
    let num=options.rawcardno
    let str=num.substring(num.length-4,num.length)
    console.log(str)
    this.setData({
      cardName:options.bank,
      cardNum:options.cardnum,
      rawcardno:options.rawcardno,
      num4:str,
      name:options.name
    })

    this.fetch()


  },
  // toBankCard(){
  //   wx.navigateTo({
  //     url: './bankCard',
  //   })
  // },

  /** 切换tab */
  // changeTab(e){
  //   let that=this
  //   let index=e.currentTarget.dataset.idx
  //   that.setData({
  //     currentTab:index
  //   })
  //   that.fetchRecord()
  // },
  /** 输入提现金额 */
  cashNum(e){
    let req=/\./
    let that=this
    console.log(req.test(e.detail.value))
    if(req.test(e.detail.value)){
    let value=e.detail.value.substring(0,e.detail.value.length-1)
    that.setData({
      cashNum:value
    })
    return value
    }else{
      that.setData({
        cashNum:e.detail.value
      })
      return e.detail.value
    }
  },

  /** 提交申请 */
  submitApply(){
  let that=this
  let cash=that.data.cashNum
  if(cash<=that.data.accountBalance){
    if (cash >= 1000) {
      if (cash % 1000 == 0) {
        wx.request({
          url: app.globalData.config.apiUrl + 'index.php?act=postWithDrawApply',
          method: 'POST',
          data: {
            openid: wx.getStorageSync('openid'),
            cash: that.data.cashNum,
            card: that.data.rawcardno,
            bank: that.data.cardName,
            name: that.data.name
          },
          success: res => {
            console.log(res.data)
            if (res.data.status) {
              wx.showModal({
                title: '',
                content: '申请提交成功，请等待审核...',
                showCancel: false,
                success: res => {
                  // that.setData({
                  //   currentTab: 1
                  // })
                  let pages=getCurrentPages()
                  let prePage=pages[pages.length-3]
                  prePage.fetchRecord()
                  wx.navigateBack({
                    delta:2
                  })

                }
              })

            } else {
              wx.showModal({
                title: '',
                content: res.data.msg,
                showCancel: false
              })
            }
          }
        })
      }else{
        let m=that.data.accountBalance-that.data.accountBalance%1000
        wx.showModal({
          title: '',
          content: '最大可申请金额为'+m,
          showCancel:false
        })
      }
    }else{
      wx.showModal({
        title: '',
        content: '申请金额至少为1000',
        showCancel:false
      })
    }
  }else{
    wx.showModal({
      title: '',
      content: '您的账户余额还不足1000元哦！',
      showCancel:false
    })
  }

   
  },

  fetch(){
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=getMyWithdrawAccount',
      method:"POST",
      data:{
        openid:wx.getStorageSync('openid')
      },
      success:res=>{
        console.log(res.data)
        // if(res.data){
          that.setData({
            accountBalance: res.data,
            canWithdraw: res.data == 0 ? 0 : (res.data - res.data % 1000)
          })
          
        // }
      }
    })
  },

  // fetchRecord(){
  //   let that=this
  //   wx.request({
  //     url: app.globalData.config.apiUrl+'index.php?act=getMyWithdrawApplyList',
  //     method:'POST',
  //     data:{
  //       openid:wx.getStorageSync('openid')
  //     },
  //     success:res=>{
  //       console.log(res.data)
  //       if(res.data){
  //         let arr=res.data.map(item=>{
  //           // let d=new Date(item.date*1000)
  //           // item.date=d.getFullYear()+'.'+(d.getMonth()+1)+'.'+d.getDate()
  //           if(item.status=="0"){
  //             item.status='审核中'
  //           }else if(item.status=="1"){
  //             item.status='通过'
  //           }else if(item.status=="-1"){
  //             item.status='驳回'
  //           }
  //           return item
  //         })
  //         console.log(arr)
          
  //         that.setData({
  //           recordlist:arr
  //         })
  //       }
  //     }
  //   })
  // },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})