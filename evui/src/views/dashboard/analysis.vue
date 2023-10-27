<template>
  <div class="ele-body">
    <el-row :gutter="15">
      <el-col :md="6" :sm="12">
        <el-card class="analysis-chart-card" shadow="never">
          <div slot="header">
            <span>未绑定API用户数量</span>
            <el-tag type="danger" size="mini" class="ele-pull-right"
              >历史</el-tag
            >
          </div>
          <div class="analysis-chart-card-num">{{ dataList.user_unused }}</div>
          <div class="analysis-chart-card-content" style="padding-top: 25px;">
            <!-- <el-progress
              :percentage="11313 / 1113"
              :show-text="false"
              :stroke-width="20"
              color="#13c2c2"
              define-back-color="#13c2c2"
              text-color="#b4d1f6"
            /> -->
            <span>还未绑定api用户共{{ dataList.user_unused }}人</span>
          </div>
          <el-divider />
          <div class="analysis-chart-card-text"></div>
        </el-card>
      </el-col>
      <el-col :md="6" :sm="12">
        <el-card class="analysis-chart-card" shadow="never">
          <div slot="header">
            <span>已绑定API用户数量</span>
            <el-tag type="danger" size="mini" class="ele-pull-right"
              >历史</el-tag
            >
          </div>
          <div class="analysis-chart-card-num">{{ dataList.user_used }}</div>
          <div class="analysis-chart-card-content" style="padding-top: 25px;">
            <!-- <el-progress
              :percentage="11313 / 1113"
              :show-text="false"
              :stroke-width="20"
              color="#13c2c2"
              define-back-color="#13c2c2"
              text-color="#b4d1f6"
            /> -->
            <span>已绑定api用户共{{ dataList.user_used }}人</span>
          </div>
          <el-divider />
          <div class="analysis-chart-card-text"></div>
        </el-card>
      </el-col>
      <el-col :md="6" :sm="12">
        <el-card class="analysis-chart-card" shadow="never">
          <div slot="header">
            <span>今日新增用户数量</span>
            <el-tag type="success" size="mini" class="ele-pull-right"
              >日</el-tag
            >
            <el-tooltip>
              <!-- <span class=" ele-pull-right" style="cursor: pointer;">
                未绑定用户数量</span
              > -->
            </el-tooltip>
          </div>
          <div class="analysis-chart-card-num quantity">
            <div>{{ dataList.day_user }}</div>
            <!-- <div>1313</div> -->
          </div>
          <div class="analysis-chart-card-content" style="padding-top: 18px;">
            <span class="ele-action">今日新增用户:12人</span>
            <!-- <span class="ele-action">绑定api用户:11人</span> -->
          </div>
          <el-divider />
          <div class="analysis-chart-card-text"></div>
        </el-card>
      </el-col>
      <el-col :md="6" :sm="12">
        <el-card class="analysis-chart-card" shadow="never">
          <div slot="header">
            <span>今日新增绑定API用户数量</span>
            <el-tag type="success" size="mini" class="ele-pull-right"
              >日</el-tag
            >
          </div>
          <div class="analysis-chart-card-num">{{ dataList.day_api_user }}</div>
          <div class="analysis-chart-card-content" style="padding-top: 25px;">
            <!-- <el-progress
              :percentage="11313 / 1113"
              :show-text="false"
              :stroke-width="20"
              color="#13c2c2"
              define-back-color="#13c2c2"
              text-color="#b4d1f6"
            /> -->
            <span>今日新增绑定api用户{{ dataList.day_api_user }}人</span>
          </div>
          <el-divider />
          <div class="analysis-chart-card-text"></div>
        </el-card>
      </el-col>
    </el-row>
    <el-row :gutter="15">
      <el-col :md="6" :sm="12">
        <el-card class="analysis-chart-card" shadow="never">
          <div slot="header">
            <span>历史充值</span>
            <el-tag type="danger" size="mini" class="ele-pull-right"
              >历史</el-tag
            >
            <el-tooltip> </el-tooltip>
          </div>
          <div class="analysis-chart-card-num quantity">
            <div>{{ dataList.all_trans }}</div>
          </div>
          <div class="analysis-chart-card-content" style="padding-top: 18px;">
            <span class="ele-action"
              >历史充值金额{{ dataList.all_trans }} USDT</span
            >
          </div>
          <el-divider />
          <div class="analysis-chart-card-text"></div>
        </el-card>
      </el-col>
      <el-col :md="6" :sm="12">
        <el-card class="analysis-chart-card" shadow="never">
          <div slot="header">
            <span>今日充值</span>
            <el-tag type="success" size="mini" class="ele-pull-right"
              >日</el-tag
            >
            <el-tooltip> </el-tooltip>
          </div>
          <div class="analysis-chart-card-num quantity">
            <div>{{ dataList.day_trans }}</div>
          </div>
          <div class="analysis-chart-card-content" style="padding-top: 18px;">
            <span class="ele-action"
              >今日充值金额{{ dataList.day_trans }} USDT</span
            >
          </div>
          <el-divider />
          <div class="analysis-chart-card-text"></div>
        </el-card>
      </el-col>

      <el-col :md="6" :sm="12">
        <el-card class="analysis-chart-card" shadow="never">
          <div slot="header">
            <span>历史提现</span>
            <el-tag type="danger" size="mini" class="ele-pull-right"
              >历史</el-tag
            >
            <el-tooltip> </el-tooltip>
          </div>
          <div class="analysis-chart-card-num quantity">
            <div>{{ dataList.all_cash }}</div>
          </div>
          <div class="analysis-chart-card-content" style="padding-top: 18px;">
            <span class="ele-action"
              >历史提醒按金额{{ dataList.all_cash }} USDT</span
            >
          </div>
          <el-divider />
          <div class="analysis-chart-card-text"></div>
        </el-card>
      </el-col>
      <el-col :md="6" :sm="12">
        <el-card class="analysis-chart-card" shadow="never">
          <div slot="header">
            <span>今日提现</span>
            <el-tag type="success" size="mini" class="ele-pull-right"
              >日</el-tag
            >
            <el-tooltip> </el-tooltip>
          </div>
          <div class="analysis-chart-card-num quantity">
            <div>{{ dataList.day_cash }}</div>
          </div>
          <div class="analysis-chart-card-content" style="padding-top: 18px;">
            <span class="ele-action"
              >今日提现金额{{ dataList.day_cash }} USDT</span
            >
          </div>
          <el-divider />
          <div class="analysis-chart-card-text"></div>
        </el-card>
      </el-col>
    </el-row>
<!--    <el-row :gutter="15">-->
<!--      <el-col :md="6" :sm="12">-->
<!--        <el-card class="analysis-chart-card" shadow="never">-->
<!--          <div slot="header">-->
<!--            <span>历史直推奖励</span>-->
<!--            <el-tag type="danger" size="mini" class="ele-pull-right"-->
<!--              >历史</el-tag-->
<!--            >-->
<!--            <el-tooltip> </el-tooltip>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-num quantity">-->
<!--            <div>{{ dataList.qb_child_all }}</div>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-content" style="padding-top: 18px;">-->
<!--            <span class="ele-action"-->
<!--              >历史直推奖励{{ dataList.qb_child_all }}USDT</span-->
<!--            >-->
<!--          </div>-->
<!--          <el-divider />-->
<!--          <div class="analysis-chart-card-text"></div>-->
<!--        </el-card>-->
<!--      </el-col>-->
<!--      <el-col :md="6" :sm="12">-->
<!--        <el-card class="analysis-chart-card" shadow="never">-->
<!--          <div slot="header">-->
<!--            <span>今日直推奖励</span>-->
<!--            <el-tag type="success" size="mini" class="ele-pull-right"-->
<!--              >日</el-tag-->
<!--            >-->
<!--            <el-tooltip> </el-tooltip>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-num quantity">-->
<!--            <div>{{ dataList.qb_child_day }}</div>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-content" style="padding-top: 18px;">-->
<!--            <span class="ele-action"-->
<!--              >今日直推奖励{{ dataList.qb_child_day }}USDT</span-->
<!--            >-->
<!--          </div>-->
<!--          <el-divider />-->
<!--          <div class="analysis-chart-card-text"></div>-->
<!--        </el-card>-->
<!--      </el-col>-->

<!--      <el-col :md="6" :sm="12">-->
<!--        <el-card class="analysis-chart-card" shadow="never">-->
<!--          <div slot="header">-->
<!--            <span>历史团队奖励</span>-->
<!--            <el-tag type="danger" size="mini" class="ele-pull-right"-->
<!--              >历史</el-tag-->
<!--            >-->
<!--            <el-tooltip> </el-tooltip>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-num quantity">-->
<!--            <div>{{ dataList.qb_team_all }}</div>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-content" style="padding-top: 18px;">-->
<!--            <span class="ele-action"-->
<!--              >历史团队奖励{{ dataList.qb_team_all }}USDT</span-->
<!--            >-->
<!--          </div>-->
<!--          <el-divider />-->
<!--          <div class="analysis-chart-card-text"></div>-->
<!--        </el-card>-->
<!--      </el-col>-->
<!--      <el-col :md="6" :sm="12">-->
<!--        <el-card class="analysis-chart-card" shadow="never">-->
<!--          <div slot="header">-->
<!--            <span>今日团队奖励</span>-->
<!--            <el-tag type="success" size="mini" class="ele-pull-right"-->
<!--              >日</el-tag-->
<!--            >-->
<!--            <el-tooltip> </el-tooltip>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-num quantity">-->
<!--            <div>{{ dataList.qb_team_day }}</div>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-content" style="padding-top: 18px;">-->
<!--            <span class="ele-action"-->
<!--              >今日团队奖励{{ dataList.qb_team_day }}USDT</span-->
<!--            >-->
<!--          </div>-->
<!--          <el-divider />-->
<!--          <div class="analysis-chart-card-text"></div>-->
<!--        </el-card>-->
<!--      </el-col>-->
<!--    </el-row>-->
<!--    <el-row :gutter="15">-->
<!--      <el-col :md="6" :sm="12">-->
<!--        <el-card class="analysis-chart-card" shadow="never">-->
<!--          <div slot="header">-->
<!--            <span>历史盈利总额</span>-->
<!--            <el-tag type="danger" size="mini" class="ele-pull-right"-->
<!--              >历史</el-tag-->
<!--            >-->
<!--            <el-tooltip> </el-tooltip>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-num quantity">-->
<!--            <div>{{ dataList.qb_profit_all }}</div>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-content" style="padding-top: 18px;">-->
<!--            <span class="ele-action"-->
<!--              >历史盈利总额度{{ dataList.qb_profit_all }}USDt</span-->
<!--            >-->
<!--          </div>-->
<!--          <el-divider />-->
<!--          <div class="analysis-chart-card-text"></div>-->
<!--        </el-card>-->
<!--      </el-col>-->
<!--      <el-col :md="6" :sm="12">-->
<!--        <el-card class="analysis-chart-card" shadow="never">-->
<!--          <div slot="header">-->
<!--            <span>今日盈利总额</span>-->
<!--            <el-tag type="success" size="mini" class="ele-pull-right"-->
<!--              >日</el-tag-->
<!--            >-->
<!--            <el-tooltip> </el-tooltip>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-num quantity">-->
<!--            <div>{{ dataList.qb_profit_day }}</div>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-content" style="padding-top: 18px;">-->
<!--            <span class="ele-action">今日盈利总额度</span>-->
<!--          </div>-->
<!--          <el-divider />-->
<!--          <div class="analysis-chart-card-text"></div>-->
<!--        </el-card>-->
<!--      </el-col>-->
<!--      <el-col :md="6" :sm="12">-->
<!--        <el-card class="analysis-chart-card" shadow="never">-->
<!--          <div slot="header">-->
<!--            <span>策略开启数量</span>-->
<!--            <el-tag type="danger" size="mini" class="ele-pull-right"-->
<!--              >当前</el-tag-->
<!--            >-->
<!--            <el-tooltip> </el-tooltip>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-num quantity">-->
<!--            <div>{{ dataList.run_setup }}</div>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-content" style="padding-top: 18px;">-->
<!--            <span class="ele-action"-->
<!--              >当前策略开启数量{{ dataList.run_setup }}条</span-->
<!--            >-->
<!--          </div>-->
<!--          <el-divider />-->
<!--          <div class="analysis-chart-card-text"></div>-->
<!--        </el-card>-->
<!--      </el-col>-->
<!--      <el-col :md="6" :sm="12">-->
<!--        <el-card class="analysis-chart-card" shadow="never">-->
<!--          <div slot="header">-->
<!--            <span>余额总量</span>-->
<!--            <el-tag type="danger" size="mini" class="ele-pull-right"-->
<!--              >当前</el-tag-->
<!--            >-->
<!--            <el-tooltip> </el-tooltip>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-num quantity">-->
<!--            <div>{{ dataList.qb_usdt_all }}</div>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-content" style="padding-top: 18px;">-->
<!--            <span class="ele-action"-->
<!--              >当前余额共计{{ dataList.qb_usdt_all }}SUDT</span-->
<!--            >-->
<!--          </div>-->
<!--          <el-divider />-->
<!--          <div class="analysis-chart-card-text"></div>-->
<!--        </el-card>-->
<!--      </el-col>-->

<!--      &lt;!&ndash; <el-col :md="6" :sm="12">-->
<!--        <el-card class="analysis-chart-card" shadow="never">-->
<!--          <div slot="header">-->
<!--            <span>历史团队奖励</span>-->
<!--          </div>-->
<!--          <div class="analysis-chart-card-num">78%</div>-->
<!--          <div-->
<!--            class="analysis-chart-card-content"-->
<!--            style="padding-top: 25px;"-->
<!--          ></div>-->
<!--          <el-divider />-->
<!--          <div class="analysis-chart-card-text"></div>-->
<!--        </el-card>-->
<!--      </el-col> &ndash;&gt;-->
<!--    </el-row>-->
    <!-- 头部工具栏 -->
    <!-- <el-card shadow="never" body-style="padding:0;">
      <div class="ele-cell demo-monitor-tool">
        <div class="ele-cell-content">
          <el-tabs
            v-model="saleSearch.type"
            class="demo-monitor-tabs"
            @tab-click="onSaleTypeChange"
          >
            <el-tab-pane label="销售额" name="saleroom" />
            <el-tab-pane label="访问量" name="visits" />
          </el-tabs>
        </div>
        <div class="ele-inline-block hidden-xs-only">
          <el-radio-group v-model="saleSearch.dateType" size="small">
            <el-radio-button :label="0">今天</el-radio-button>
            <el-radio-button :label="1">本周</el-radio-button>
            <el-radio-button :label="2">本月</el-radio-button>
            <el-radio-button :label="3">本年</el-radio-button>
          </el-radio-group>
        </div>
        <div class="ele-inline-block hidden-xs-only">
          <el-date-picker
            v-model="saleSearch.datetime"
            type="daterange"
            range-separator="至"
            size="small"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            unlink-panels
          />
        </div>
      </div>
      <el-divider />
      <el-row>
        <el-col :md="18" :sm="16">
          <div class="demo-monitor-title">
            {{ { saleroom: '销售', visits: '访问量' }[saleSearch.type] }}趋势
          </div>
          <ele-chart :option="saleChartOption" style="height:285px;" />
        </el-col>
        <el-col :md="6" :sm="8">
          <div class="demo-monitor-title">
            门店{{
              { saleroom: '销售额', visits: '访问量' }[saleSearch.type]
            }}排名
          </div>
          <div
            v-for="(item, index) in saleroomRankData"
            :key="index"
            class="demo-monitor-rank-item ele-cell"
          >
            <el-tag
              :type="index < 3 ? '' : 'info'"
              size="mini"
              class="ele-tag-round"
              >{{ index + 1 }}</el-tag
            >
            <div class="ele-cell-content">{{ item.name }}</div>
            <div class="ele-text-secondary">{{ item.value }}</div>
          </div>
        </el-col>
      </el-row>
    </el-card>
    <el-row :gutter="15">
      <el-col :md="18" :sm="16">
        <el-card
          shadow="never"
          header="最近1小时访问情况"
          body-style="padding:10px 5px 0 0;"
        >
          <ele-chart :option="visitHourChartOption" style="height:323px;" />
        </el-card>
      </el-col>
      <el-col :md="6" :sm="8">
        <el-card shadow="never" header="热门搜索">
          <ele-word-cloud :data="hotSearchData" style="height:303px;" />
        </el-card>
      </el-col>
    </el-row> -->
  </div>
</template>

<script>
// import EleChart from '@/components/EleChart'
// import EleWordCloud from '@/components/EleChart/EleWordCloud'

export default {
  name: 'Analysis',
  // components: { EleChart, EleWordCloud },
  data() {
    return {
      dataList: {},
      // payNumData: [], // 支付笔数
      // saleSearch: { type: 'saleroom', dateType: 0, datetime: '' }, // 销售量搜索参数
      // saleroomData: [], // 销售量数据
      // saleroomRankData: [], // 销售量排名数据
      // visitHourData: [], // 最近1小时访问情况数据
      // hotSearchData: [], // 词云数据
    }
  },
  created() {
    this.getdataList()
  },
  computed: {
    // /* 访问量折线图配置 */
    // visitChartOption() {
    //   return {
    //     color: '#975fe5',
    //     tooltip: {
    //       trigger: 'axis',
    //       formatter:
    //         '<i class="ele-chart-dot" style="background-color:#975fe5;"></i>{b0}: {c0}',
    //     },
    //     grid: { top: 10, bottom: 0, left: 0, right: 0 },
    //     xAxis: [
    //       {
    //         show: false,
    //         type: 'category',
    //         boundaryGap: false,
    //         data: this.payNumData.map((d) => d.date),
    //       },
    //     ],
    //     yAxis: [
    //       {
    //         show: false,
    //         type: 'value',
    //         splitLine: { show: false },
    //       },
    //     ],
    //     series: [
    //       {
    //         type: 'line',
    //         smooth: true,
    //         animation: false,
    //         symbol: 'none',
    //         areaStyle: { opacity: 0.6 },
    //         data: this.payNumData.map((d) => d.value),
    //       },
    //     ],
    //   }
    // },
    // /* 支付笔数柱状图配置 */
    // payNumChartOption() {
    //   return {
    //     tooltip: {
    //       trigger: 'axis',
    //       formatter:
    //         '<i class="ele-chart-dot" style="background-color:#3aa1ff;"></i>{b0}: {c0}',
    //     },
    //     grid: { top: 10, bottom: 0, left: 0, right: 0 },
    //     xAxis: [
    //       {
    //         show: false,
    //         type: 'category',
    //         data: this.payNumData.map((d) => d.date),
    //       },
    //     ],
    //     yAxis: [
    //       {
    //         show: false,
    //         type: 'value',
    //         splitLine: { show: false },
    //       },
    //     ],
    //     series: [
    //       {
    //         type: 'bar',
    //         data: this.payNumData.map((d) => d.value),
    //       },
    //     ],
    //   }
    // },
    // /* 销售额柱状图配置 */
    // saleChartOption() {
    //   return {
    //     tooltip: { trigger: 'axis' },
    //     xAxis: [
    //       {
    //         type: 'category',
    //         data: this.saleroomData.map((d) => d.month),
    //       },
    //     ],
    //     yAxis: [{ type: 'value' }],
    //     series: [
    //       {
    //         type: 'bar',
    //         data: this.saleroomData.map((d) => d.value),
    //       },
    //     ],
    //   }
    // },
    // /* 最近1小时访问情况折线图配置 */
    // visitHourChartOption() {
    //   return {
    //     tooltip: { trigger: 'axis' },
    //     legend: { data: ['浏览量', '访问量'], right: 20 },
    //     xAxis: [
    //       {
    //         type: 'category',
    //         boundaryGap: false,
    //         data: this.visitHourData.map((d) => d.time),
    //       },
    //     ],
    //     yAxis: [{ type: 'value' }],
    //     series: [
    //       {
    //         name: '浏览量',
    //         type: 'line',
    //         smooth: true,
    //         symbol: 'none',
    //         areaStyle: { opacity: 0.5 },
    //         data: this.visitHourData.map((d) => d.views),
    //       },
    //       {
    //         name: '访问量',
    //         type: 'line',
    //         smooth: true,
    //         symbol: 'none',
    //         areaStyle: { opacity: 0.5 },
    //         data: this.visitHourData.map((d) => d.visits),
    //       },
    //     ],
    //   }
    // },
  },
  mounted() {
    // this.getPayNumData()
    // this.getSaleroomData()
    // this.getVisitHourData()
    // this.getWordCloudData()
  },
  methods: {
    getdataList() {
      this.$http.get('index/analysis').then((res) => {
        // console.log(res.data.code)
        if (res.data.code === 0) {
          this.dataList = res.data.data
        }
      })
    },
    // /* 获取支付笔数数据 */
    // getPayNumData() {
    //   this.payNumData = [
    //     { date: '2020-06-12', value: 7 },
    //     { date: '2020-06-13', value: 5 },
    //     { date: '2020-06-14', value: 4 },
    //     { date: '2020-06-15', value: 2 },
    //     { date: '2020-06-16', value: 4 },
    //     { date: '2020-06-17', value: 7 },
    //     { date: '2020-06-18', value: 5 },
    //     { date: '2020-06-19', value: 6 },
    //     { date: '2020-06-20', value: 5 },
    //     { date: '2020-06-21', value: 9 },
    //     { date: '2020-06-22', value: 6 },
    //     { date: '2020-06-23', value: 3 },
    //     { date: '2020-06-24', value: 1 },
    //     { date: '2020-06-25', value: 5 },
    //     { date: '2020-06-26', value: 3 },
    //     { date: '2020-06-27', value: 6 },
    //     { date: '2020-06-18', value: 5 },
    //   ]
    // },
    // /* 获取销售量数据 */
    // getSaleroomData() {
    //   if (this.saleSearch.type === 'saleroom') {
    //     this.saleroomData = [
    //       { month: '1月', value: 816 },
    //       { month: '2月', value: 542 },
    //       { month: '3月', value: 914 },
    //       { month: '4月', value: 781 },
    //       { month: '5月', value: 355 },
    //       { month: '6月', value: 796 },
    //       { month: '7月', value: 714 },
    //       { month: '8月', value: 1195 },
    //       { month: '9月', value: 1055 },
    //       { month: '10月', value: 271 },
    //       { month: '11月', value: 384 },
    //       { month: '12月', value: 1098 },
    //     ]
    //   } else {
    //     this.saleroomData = [
    //       { month: '1月', value: 1098 },
    //       { month: '2月', value: 384 },
    //       { month: '3月', value: 271 },
    //       { month: '4月', value: 1055 },
    //       { month: '5月', value: 1195 },
    //       { month: '6月', value: 714 },
    //       { month: '7月', value: 796 },
    //       { month: '8月', value: 355 },
    //       { month: '9月', value: 781 },
    //       { month: '10月', value: 914 },
    //       { month: '11月', value: 542 },
    //       { month: '12月', value: 816 },
    //     ]
    //   }
    //   this.saleroomRankData = [
    //     { name: '工专路 1 号店', value: '323,234' },
    //     { name: '工专路 2 号店', value: '323,234' },
    //     { name: '工专路 3 号店', value: '323,234' },
    //     { name: '工专路 4 号店', value: '323,234' },
    //     { name: '工专路 5 号店', value: '323,234' },
    //     { name: '工专路 6 号店', value: '323,234' },
    //     { name: '工专路 7 号店', value: '323,234' },
    //   ]
    // },
    // /* 获取最近1小时访问情况数据 */
    // getVisitHourData() {
    //   this.visitHourData = [
    //     { time: '16:00', visits: 15, views: 45 },
    //     { time: '16:05', visits: 39, views: 169 },
    //     { time: '16:10', visits: 152, views: 400 },
    //     { time: '16:15', visits: 94, views: 285 },
    //     { time: '16:20', visits: 102, views: 316 },
    //     { time: '16:25', visits: 86, views: 148 },
    //     { time: '16:30', visits: 39, views: 150 },
    //     { time: '16:35', visits: 38, views: 234 },
    //     { time: '16:40', visits: 95, views: 158 },
    //     { time: '16:45', visits: 30, views: 100 },
    //     { time: '16:50', visits: 86, views: 266 },
    //   ]
    // },
    // /* 获取词云数据 */
    // getWordCloudData() {
    //   this.hotSearchData = [
    //     { name: '软妹子', value: 23 },
    //     { name: '汪星人', value: 23 },
    //     { name: '长腿欧巴', value: 23 },
    //     { name: '萝莉', value: 22 },
    //     { name: '辣~', value: 22 },
    //     { name: 'K歌', value: 22 },
    //     { name: '大长腿', value: 21 },
    //     { name: '川妹子', value: 21 },
    //     { name: '女神', value: 21 },
    //     { name: '米粉', value: 20 },
    //     { name: '专注设计', value: 20 },
    //     { name: '逛街', value: 20 },
    //     { name: '黑长直', value: 20 },
    //     { name: '海纳百川', value: 19 },
    //     { name: '萌萌哒', value: 19 },
    //     { name: '坚持', value: 19 },
    //     { name: '话唠', value: 19 },
    //     { name: '果粉', value: 18 },
    //     { name: '喵星人', value: 18 },
    //     { name: '花粉', value: 18 },
    //     { name: '衬衫控', value: 18 },
    //     { name: '宅男', value: 17 },
    //     { name: '小清新', value: 17 },
    //     { name: '眼镜男', value: 17 },
    //     { name: '琼瑶', value: 17 },
    //     { name: '穷游党', value: 16 },
    //     { name: '铲屎官', value: 16 },
    //     { name: '正太', value: 16 },
    //     { name: '中二病', value: 16 },
    //     { name: '夜猫子', value: 15 },
    //     { name: '逗比', value: 15 },
    //     { name: '腹黑', value: 15 },
    //     { name: '吃鸡', value: 15 },
    //     { name: '为了联盟', value: 14 },
    //     { name: '背包客', value: 14 },
    //     { name: '民谣', value: 14 },
    //     { name: '为了部落', value: 14 },
    //     { name: '懒癌患者', value: 13 },
    //     { name: '追剧', value: 13 },
    //     { name: 'IT民工', value: 13 },
    //     { name: 'CNB成员', value: 13 },
    //     { name: '选择困难', value: 12 },
    //     { name: '锤粉', value: 12 },
    //     { name: '欧皇', value: 12 },
    //     { name: '仙气十足', value: 12 },
    //   ]
    // },
    // /* 销售量tab选择改变事件 */
    // onSaleTypeChange() {
    //   this.getSaleroomData()
    // },
  },
}
</script>

<style scoped>
.quantity {
  display: flex;
  justify-content: space-between;
}
.ele-body {
  padding-bottom: 0;
}

.el-card {
  margin-bottom: 15px;
}

/** 统计卡片 */
.analysis-chart-card-num {
  font-size: 30px;
}

.analysis-chart-card-content {
  height: 40px;
  box-sizing: border-box;
  margin-bottom: 12px;
}

.analysis-chart-card-text {
  padding-top: 12px;
}

/** 销售额、访问量工具栏 */
.demo-monitor-tool {
  padding: 0 20px;
}

.demo-monitor-tool >>> .el-tabs__nav-wrap:after {
  display: none;
}

.demo-monitor-tool >>> .el-tabs__item {
  height: 50px;
  line-height: 50px;
  font-size: 15px;
}

.demo-monitor-tool .el-date-editor {
  width: 256px;
  margin-left: 10px;
}

/** 小标题 */
.demo-monitor-title {
  padding: 0 20px;
  margin: 15px 0 5px 0;
}

/** 排名item */
.demo-monitor-rank-item {
  padding: 0 20px;
  line-height: 20px;
  margin-top: 18px;
}
</style>
