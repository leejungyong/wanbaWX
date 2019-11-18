 const app=getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    status:'',
    type:'',
    city:'',
    reason:'',
    currentIndex:0,
    myClients:[],
    indicatorDots: false,//是否显示面板指示点
    vertical: false,  //滑动方向是否为纵向
    autoplay: true,   //是否自动播放
    duration: 100,    //动画滑动时长
    interval: 2500,   //自动切换时间间隔
    circular: false,   //是否衔接滑动
    imgheights: [],
    current: 0,
    swiper:[],

    monthSales:[],   //月销售次数
    monthClients:[],   //月客户开发

    seasonSales:[],    //季度销售次数
    seasonClients:[]   //季度客户开发


  },

  /**去往历史记录页面 */
  toHistory(){
    wx.navigateTo({
      url: './historyRecord',
    })
  },
  to: function (e) {
    let that = this
    let id = e.currentTarget.id
    let url = that.data.swiper[id].url
    console.log(url)
    wx.navigateTo({
      url: '/pages/index/showWx?url=' + url
    })
  },

  bindchange: function (e) {
    // console.log(e.detail.current)
    this.setData({
      current: e.detail.current
    })
  },

  imageLoad: function (e) { //获取图片真实宽度  
    var imgwidth = e.detail.width,
      imgheight = e.detail.height,
      //宽高比  
      ratio = imgwidth / imgheight;
    //console.log(imgwidth, imgheight)
    //计算的高度值  
    var viewHeight = 750 / ratio;
    var imgheight = viewHeight;
    var imgheights = this.data.imgheights;
    //把每一张图片的对应的高度记录到数组里  
    imgheights[e.target.dataset.id] = imgheight;
    this.setData({
      imgheights: imgheights
    })
  },
  /**
   * 用户点击tab时调用
   */
  tabClick(e){
    console.log(e)
    this.setData({
      currentIndex:e.currentTarget.dataset.idx
    })
  },

  /**点击客户的记录 */
  clickRecord(e){
    let that=this
    let id=e.currentTarget.dataset.id
    let saleList=this.data.myClients[id].saleList
    // console.log(saleList)
    wx.navigateTo({
      url: './clientRecord?saleList='+JSON.stringify(saleList)+'&swiper='+JSON.stringify(that.data.swiper),
    })
  },
  /**
   * 页面初始化时获取数据 
  */
  fetch(){
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl +'index.php?act=getMyAgentStatistics',
      method:'POST',
      data:{
        openid:wx.getStorageSync('openid'),
        token:''
      },
      success:res=>{
        console.log(res)
        that.setData({
         myClients:res.data.myClients,
         swiper:res.data.swiper,
         monthSales:res.data.monthData.monthSales,
         monthClients: res.data.monthData.monthClients,
         seasonSales: res.data.seasonData.seasonSales,
         seasonClients:res.data.seasonData.seasonClients
        })
      }

    })
  },

  /**
   * 季度销售次数
   */
  toSaleNumofseason(){
    let that=this
    if(that.data.seasonSales.length>0){
      wx.navigateTo({
        url: './saleDetail?type=sale&list=' + JSON.stringify(that.data.seasonSales),
      })
    }
  },
  /**季度客户开发 */
  toClientDevelopofseason(){
    let that = this
    if(that.data.seasonClients.length>0){
      wx.navigateTo({
        url: './saleDetail?type=client&list=' + JSON.stringify(that.data.seasonClients),
      })
    }
  },
  /**本月销售次数 */
  toSaleNumofmonth(){
    let that = this
    if(that.data.monthSales.length>0){
      wx.navigateTo({
        url: './saleDetail?type=sale&list=' + JSON.stringify(that.data.monthSales),
      })
    }
  },
  /**本月客户开发 */
  toClientDevelopofmonth(){
    let that = this
    if(that.data.monthClients.length>0){
      wx.navigateTo({
        url: './saleDetail?type=client&list=' + JSON.stringify(that.data.monthClients),
      })
    }
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let status = options.status != undefined ? options.status : ''
    let openid = wx.getStorageSync('openid')
    let type = options.type != undefined ? options.type : ''
    let reason = options.reason != undefined ? options.reason : ''
    let city=''
    if(options.city){
      let arr = options.city.split(' ')
      city = arr[0] + arr[1]
    }
  this.setData({
      type:type,
      status:status,
      city: city,
      reason:reason
    })
    this.fetch()
  },

  /**
   * 修改申请
   */
  alterApply() {
    let that = this
    wx.navigateTo({
      url: '../cityPartner/cityPartner?alter=1',
    })
  },

  /**
   * 通过 返回
   */
  returnLastPage(){
    wx.navigateBack({
    })
  },
  /**
   * 点击下一步
   */
  nextStep: function () {
    let that = this
    wx.navigateTo({
      url: '../cityPartner/cityPartner',
    })
  },

  /**
   * 重新申请
   */
  reapply(){
    let that = this
    wx.navigateTo({
      url: '../cityPartner/cityPartner?alter=2',
    })
  },

  onShareAppMessage: function (ops) {
    let that = this
    //console.log(ops)
    if (ops.from === 'button') {
    
    }
    return {
      title: '邀请您加入玩霸江湖',
      path: '/pages/cityPartner/invite?from='+wx.getStorageSync('openid'),
      imageUrl: app.globalData.config.imgUrl + 'wanba/img/sharepic/1.jpg'
    }
  },


})