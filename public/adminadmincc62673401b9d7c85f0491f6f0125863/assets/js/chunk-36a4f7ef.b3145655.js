(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-36a4f7ef"],{"1c35":function(a,t,s){},"6acf":function(a,t,s){"use strict";s.r(t);var i=function(){var a=this,t=a.$createElement,s=a._self._c||t;return s("div",{staticClass:"ele-body"},[s("el-row",{attrs:{gutter:15}},[s("el-col",{attrs:{md:6,sm:12}},[s("el-card",{staticClass:"analysis-chart-card",attrs:{shadow:"never"}},[s("div",{attrs:{slot:"header"},slot:"header"},[s("span",[a._v("未绑定API用户数量")]),s("el-tag",{staticClass:"ele-pull-right",attrs:{type:"danger",size:"mini"}},[a._v("历史")])],1),s("div",{staticClass:"analysis-chart-card-num"},[a._v(a._s(a.dataList.user_unused))]),s("div",{staticClass:"analysis-chart-card-content",staticStyle:{"padding-top":"25px"}},[s("span",[a._v("还未绑定api用户共"+a._s(a.dataList.user_unused)+"人")])]),s("el-divider"),s("div",{staticClass:"analysis-chart-card-text"})],1)],1),s("el-col",{attrs:{md:6,sm:12}},[s("el-card",{staticClass:"analysis-chart-card",attrs:{shadow:"never"}},[s("div",{attrs:{slot:"header"},slot:"header"},[s("span",[a._v("已绑定API用户数量")]),s("el-tag",{staticClass:"ele-pull-right",attrs:{type:"danger",size:"mini"}},[a._v("历史")])],1),s("div",{staticClass:"analysis-chart-card-num"},[a._v(a._s(a.dataList.user_used))]),s("div",{staticClass:"analysis-chart-card-content",staticStyle:{"padding-top":"25px"}},[s("span",[a._v("已绑定api用户共"+a._s(a.dataList.user_used)+"人")])]),s("el-divider"),s("div",{staticClass:"analysis-chart-card-text"})],1)],1),s("el-col",{attrs:{md:6,sm:12}},[s("el-card",{staticClass:"analysis-chart-card",attrs:{shadow:"never"}},[s("div",{attrs:{slot:"header"},slot:"header"},[s("span",[a._v("今日新增用户数量")]),s("el-tag",{staticClass:"ele-pull-right",attrs:{type:"success",size:"mini"}},[a._v("日")]),s("el-tooltip")],1),s("div",{staticClass:"analysis-chart-card-num quantity"},[s("div",[a._v(a._s(a.dataList.day_user))])]),s("div",{staticClass:"analysis-chart-card-content",staticStyle:{"padding-top":"18px"}},[s("span",{staticClass:"ele-action"},[a._v("今日新增用户:12人")])]),s("el-divider"),s("div",{staticClass:"analysis-chart-card-text"})],1)],1),s("el-col",{attrs:{md:6,sm:12}},[s("el-card",{staticClass:"analysis-chart-card",attrs:{shadow:"never"}},[s("div",{attrs:{slot:"header"},slot:"header"},[s("span",[a._v("今日新增绑定API用户数量")]),s("el-tag",{staticClass:"ele-pull-right",attrs:{type:"success",size:"mini"}},[a._v("日")])],1),s("div",{staticClass:"analysis-chart-card-num"},[a._v(a._s(a.dataList.day_api_user))]),s("div",{staticClass:"analysis-chart-card-content",staticStyle:{"padding-top":"25px"}},[s("span",[a._v("今日新增绑定api用户"+a._s(a.dataList.day_api_user)+"人")])]),s("el-divider"),s("div",{staticClass:"analysis-chart-card-text"})],1)],1)],1),s("el-row",{attrs:{gutter:15}},[s("el-col",{attrs:{md:6,sm:12}},[s("el-card",{staticClass:"analysis-chart-card",attrs:{shadow:"never"}},[s("div",{attrs:{slot:"header"},slot:"header"},[s("span",[a._v("历史充值")]),s("el-tag",{staticClass:"ele-pull-right",attrs:{type:"danger",size:"mini"}},[a._v("历史")]),s("el-tooltip")],1),s("div",{staticClass:"analysis-chart-card-num quantity"},[s("div",[a._v(a._s(a.dataList.all_trans))])]),s("div",{staticClass:"analysis-chart-card-content",staticStyle:{"padding-top":"18px"}},[s("span",{staticClass:"ele-action"},[a._v("历史充值金额"+a._s(a.dataList.all_trans)+" USDT")])]),s("el-divider"),s("div",{staticClass:"analysis-chart-card-text"})],1)],1),s("el-col",{attrs:{md:6,sm:12}},[s("el-card",{staticClass:"analysis-chart-card",attrs:{shadow:"never"}},[s("div",{attrs:{slot:"header"},slot:"header"},[s("span",[a._v("今日充值")]),s("el-tag",{staticClass:"ele-pull-right",attrs:{type:"success",size:"mini"}},[a._v("日")]),s("el-tooltip")],1),s("div",{staticClass:"analysis-chart-card-num quantity"},[s("div",[a._v(a._s(a.dataList.day_trans))])]),s("div",{staticClass:"analysis-chart-card-content",staticStyle:{"padding-top":"18px"}},[s("span",{staticClass:"ele-action"},[a._v("今日充值金额"+a._s(a.dataList.day_trans)+" USDT")])]),s("el-divider"),s("div",{staticClass:"analysis-chart-card-text"})],1)],1),s("el-col",{attrs:{md:6,sm:12}},[s("el-card",{staticClass:"analysis-chart-card",attrs:{shadow:"never"}},[s("div",{attrs:{slot:"header"},slot:"header"},[s("span",[a._v("历史提现")]),s("el-tag",{staticClass:"ele-pull-right",attrs:{type:"danger",size:"mini"}},[a._v("历史")]),s("el-tooltip")],1),s("div",{staticClass:"analysis-chart-card-num quantity"},[s("div",[a._v(a._s(a.dataList.all_cash))])]),s("div",{staticClass:"analysis-chart-card-content",staticStyle:{"padding-top":"18px"}},[s("span",{staticClass:"ele-action"},[a._v("历史提醒按金额"+a._s(a.dataList.all_cash)+" USDT")])]),s("el-divider"),s("div",{staticClass:"analysis-chart-card-text"})],1)],1),s("el-col",{attrs:{md:6,sm:12}},[s("el-card",{staticClass:"analysis-chart-card",attrs:{shadow:"never"}},[s("div",{attrs:{slot:"header"},slot:"header"},[s("span",[a._v("今日提现")]),s("el-tag",{staticClass:"ele-pull-right",attrs:{type:"success",size:"mini"}},[a._v("日")]),s("el-tooltip")],1),s("div",{staticClass:"analysis-chart-card-num quantity"},[s("div",[a._v(a._s(a.dataList.day_cash))])]),s("div",{staticClass:"analysis-chart-card-content",staticStyle:{"padding-top":"18px"}},[s("span",{staticClass:"ele-action"},[a._v("今日提现金额"+a._s(a.dataList.day_cash)+" USDT")])]),s("el-divider"),s("div",{staticClass:"analysis-chart-card-text"})],1)],1)],1)],1)},e=[],l={name:"Analysis",data:function(){return{dataList:{}}},created:function(){this.getdataList()},computed:{},mounted:function(){},methods:{getdataList:function(){var a=this;this.$http.get("index/analysis").then((function(t){0===t.data.code&&(a.dataList=t.data.data)}))}}},d=l,c=(s("d98c"),s("2877")),r=Object(c["a"])(d,i,e,!1,null,"3d49ffa4",null);t["default"]=r.exports},d98c:function(a,t,s){"use strict";s("1c35")}}]);