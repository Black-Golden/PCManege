(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-0ed4e38e"],{"10fa":function(e,t,o){"use strict";o.r(t);var l=function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{staticClass:"ele-body"},[o("el-card",{attrs:{shadow:"never"}},[o("el-form",{staticClass:"ele-form-search",attrs:{model:e.table.where,"label-width":"77px"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.$refs.table.reload()},submit:function(e){e.preventDefault()}}},[o("el-row",{attrs:{gutter:15}},[o("el-col",{attrs:{md:6,sm:12}},[o("el-form-item",{attrs:{label:"代币类型:"}},[o("el-input",{attrs:{placeholder:"请输入代币类型",clearable:""},model:{value:e.table.where.title,callback:function(t){e.$set(e.table.where,"title",t)},expression:"table.where.title"}})],1)],1),o("el-col",{attrs:{md:6,sm:12}},[o("div",{staticClass:"ele-form-actions"},[o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-search"},on:{click:function(t){return e.$refs.table.reload()}}},[e._v("查询 ")]),o("el-button",{on:{click:function(t){(e.table.where={})&&e.$refs.table.reload()}}},[e._v("重置")])],1)])],1)],1),o("div",{staticClass:"ele-table-tool ele-table-tool-default"},[e.permission.includes("sys:quantstrategy:add")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-plus",size:"small"},on:{click:function(t){e.showEdit=!0}}},[e._v("添加 ")]):e._e(),e.permission.includes("sys:quantstrategy:dall")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"danger",icon:"el-icon-delete",size:"small"},on:{click:function(t){return e.remove()}}},[e._v("批量删除 ")]):e._e()],1),o("ele-data-table",{ref:"table",attrs:{config:e.table,choose:e.choose,height:"calc(100vh - 315px)","highlight-current-row":""},on:{"update:choose":function(t){e.choose=t}},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.index;return[o("el-table-column",{attrs:{type:"selection",width:"45",align:"center",fixed:"left"}}),o("el-table-column",{attrs:{type:"index",index:l,label:"编号",width:"60",align:"center",fixed:"left","show-overflow-tooltip":""}}),o("el-table-column",{attrs:{prop:"title",label:"代币类型",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"rounds",label:"做单次数",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"down_stop_per",label:"止损百分比",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"down_back_per",label:"止损回调百分比",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"up_stop_per",label:"止盈百分比",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"up_back_per",label:"止盈回落百分比",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"create_time",label:"创建时间",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"update_time",label:"更新时间",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{label:"操作",width:"130px",align:"center",resizable:!1,fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.row;return[e.permission.includes("sys:quantstrategy:edit")?o("el-link",{attrs:{icon:"el-icon-edit",type:"primary",underline:!1},on:{click:function(t){return e.edit(l)}}},[e._v("修改")]):e._e(),o("el-popconfirm",{staticClass:"ele-action",attrs:{title:"确定要删除此交易策略吗？"},on:{confirm:function(t){return e.remove(l)}}},[e.permission.includes("sys:quantstrategy:delete")?o("el-link",{attrs:{slot:"reference",icon:"el-icon-delete",type:"danger",underline:!1},slot:"reference"},[e._v("删除")]):e._e()],1)]}}],null,!0)})]}}])})],1),o("el-dialog",{attrs:{title:(e.editForm.id,"修改交易策略"),visible:e.showEdit,width:"450px","destroy-on-close":!0,"lock-scroll":!1},on:{"update:visible":function(t){e.showEdit=t},closed:function(t){e.editForm={}}}},[o("el-form",{ref:"editForm",attrs:{model:e.editForm,rules:e.editRules,"label-width":"100px"}},[o("el-form-item",{attrs:{label:"代币类型：",prop:"title"}},[o("el-input",{attrs:{placeholder:"请输入代币类型",clearable:""},model:{value:e.editForm.title,callback:function(t){e.$set(e.editForm,"title",t)},expression:"editForm.title"}})],1),o("el-form-item",{attrs:{label:"做单次数：",prop:"rounds"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入做单次数"},model:{value:e.editForm.rounds,callback:function(t){e.$set(e.editForm,"rounds",t)},expression:"editForm.rounds"}})],1),o("el-form-item",{attrs:{label:"止损百分比：",prop:"down_stop_per"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入止损百分比"},model:{value:e.editForm.down_stop_per,callback:function(t){e.$set(e.editForm,"down_stop_per",t)},expression:"editForm.down_stop_per"}})],1),o("el-form-item",{attrs:{label:"止损回调百分比：",prop:"down_back_per"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入止损回调百分比"},model:{value:e.editForm.down_back_per,callback:function(t){e.$set(e.editForm,"down_back_per",t)},expression:"editForm.down_back_per"}})],1),o("el-form-item",{attrs:{label:"止盈百分比：",prop:"up_stop_per"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入止盈百分比"},model:{value:e.editForm.up_stop_per,callback:function(t){e.$set(e.editForm,"up_stop_per",t)},expression:"editForm.up_stop_per"}})],1),o("el-form-item",{attrs:{label:"止盈回落百分比：",prop:"up_back_per"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入止盈回落百分比"},model:{value:e.editForm.up_back_per,callback:function(t){e.$set(e.editForm,"up_back_per",t)},expression:"editForm.up_back_per"}})],1)],1),o("div",{attrs:{slot:"footer"},slot:"footer"},[o("el-button",{on:{click:function(t){e.showEdit=!1}}},[e._v("取消")]),o("el-button",{attrs:{type:"primary"},on:{click:e.save}},[e._v("保存")])],1)],1)],1)},r=[],s=(o("d81d"),o("5530")),a=o("2f62"),i={name:"SysQuantStrategy",data:function(){return{table:{url:"/quantstrategy/index",where:{}},choose:[],showEdit:!1,editForm:{},editRules:{title:[{required:!0,message:"请输入代币类型",trigger:"blur"}],rounds:[{required:!0,message:"请输入做单次数",trigger:"blur"}],down_stop_per:[{required:!0,message:"请输入止损百分比",trigger:"blur"}],down_back_per:[{required:!0,message:"请输入止损回调百分比",trigger:"blur"}],up_stop_per:[{required:!0,message:"请输入止盈百分比",trigger:"blur"}],up_back_per:[{required:!0,message:"请输入止盈回落百分比",trigger:"blur"}]}}},computed:Object(s["a"])({},Object(a["b"])(["permission"])),mounted:function(){},methods:{edit:function(e){var t=this;this.$http.get("/quantstrategy/info?id="+e.id).then((function(e){0===e.data.code?(t.editForm=e.data.data,t.showEdit=!0):t.$message.error(e.data.msg)})).catch((function(e){t.$message.error(e.message)}))},save:function(){var e=this;this.$refs["editForm"].validate((function(t){if(!t)return!1;var o=e.$loading({lock:!0});e.$http.post("/quantstrategy/edit",e.editForm).then((function(t){o.close(),0===t.data.code?(e.showEdit=!1,e.$message({type:"success",message:t.data.msg}),e.$refs.table.reload()):e.$message.error(t.data.msg)})).catch((function(t){o.close(),e.$message.error(t.message)}))}))},remove:function(e){var t=this;if(console.log(e),e){var o=this.$loading({lock:!0});this.$http.post("/quantstrategy/delete",{id:e.id}).then((function(e){o.close(),0===e.data.code?(t.$message({type:"success",message:e.data.msg}),t.$refs.table.reload()):t.$message.error(e.data.msg)})).catch((function(e){o.close(),t.$message.error(e.message)}))}else{if(0===this.choose.length)return this.$message.error("请至少选择一条数据");var l=this.choose.map((function(e){return e.id}));this.$confirm("确定要删除选中的交易策略吗?","提示",{type:"warning"}).then((function(){var e=t.$loading({lock:!0});t.$http.post("/quantstrategy/delete",{id:l}).then((function(o){e.close(),0===o.data.code?(t.$message({type:"success",message:o.data.msg}),t.$refs.table.reload()):t.$message.error(o.data.msg)})).catch((function(o){e.close(),t.$message.error(o.message)}))})).catch((function(){return 0}))}}}},n=i,c=(o("7676"),o("2877")),d=Object(c["a"])(n,l,r,!1,null,"2139ae5a",null);t["default"]=d.exports},7676:function(e,t,o){"use strict";o("94bd")},"94bd":function(e,t,o){}}]);