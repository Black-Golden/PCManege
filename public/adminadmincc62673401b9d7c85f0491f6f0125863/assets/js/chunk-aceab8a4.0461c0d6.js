(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-aceab8a4"],{"42dc":function(e,t,o){"use strict";o("8009")},8009:function(e,t,o){},b6f1:function(e,t,o){"use strict";o.r(t);var l=function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{staticClass:"ele-body"},[o("el-card",{attrs:{shadow:"never"}},[o("el-form",{staticClass:"ele-form-search",attrs:{model:e.table.where,"label-width":"77px"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.$refs.table.reload()},submit:function(e){e.preventDefault()}}},[o("el-row",{attrs:{gutter:15}},[o("el-col",{attrs:{md:6,sm:12}},[o("el-form-item",{attrs:{label:"通知标题:"}},[o("el-input",{attrs:{placeholder:"请输入通知标题",clearable:""},model:{value:e.table.where.title,callback:function(t){e.$set(e.table.where,"title",t)},expression:"table.where.title"}})],1)],1),o("el-col",{attrs:{md:6,sm:12}},[o("div",{staticClass:"ele-form-actions"},[o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-search"},on:{click:function(t){return e.$refs.table.reload()}}},[e._v("查询 ")]),o("el-button",{on:{click:function(t){(e.table.where={})&&e.$refs.table.reload()}}},[e._v("重置")])],1)])],1)],1),o("div",{staticClass:"ele-table-tool ele-table-tool-default"},[e.permission.includes("sys:document:add")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"primary",icon:"el-icon-plus",size:"small"},on:{click:function(t){e.showEdit=!0}}},[e._v("添加 ")]):e._e(),e.permission.includes("sys:document:dall")?o("el-button",{staticClass:"ele-btn-icon",attrs:{type:"danger",icon:"el-icon-delete",size:"small"},on:{click:function(t){return e.remove()}}},[e._v("批量删除 ")]):e._e()],1),o("ele-data-table",{ref:"table",attrs:{config:e.table,choose:e.choose,height:"calc(100vh - 315px)","highlight-current-row":""},on:{"update:choose":function(t){e.choose=t}},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.index;return[o("el-table-column",{attrs:{type:"selection",width:"45",align:"center",fixed:"left"}}),o("el-table-column",{attrs:{type:"index",index:l,label:"编号",width:"60",align:"center",fixed:"left","show-overflow-tooltip":""}}),o("el-table-column",{attrs:{prop:"title",label:"通知标题",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"content",label:"通知内容",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"is_top",label:"置顶",sortable:"custom",resizable:!1,"min-width":"120"},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.row;return[o("div",[e._v(e._s(1==l.is_top?"是":"否"))])]}}],null,!0)}),o("el-table-column",{attrs:{prop:"browse",label:"阅读量",sortable:"custom","show-overflow-tooltip":"","min-width":"120"}}),o("el-table-column",{attrs:{prop:"status",label:"状态",sortable:"custom",resizable:!1,"min-width":"120"},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.row;return[o("div",[e._v(e._s(1==l.status?"是":"否"))])]}}],null,!0)}),o("el-table-column",{attrs:{prop:"type",label:"文档类型",sortable:"custom","show-overflow-tooltip":"","min-width":"120"},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.row;return[o("div",[e._v(e._s(1==l.type?"官方文档":"使用教程"))])]}}],null,!0)}),o("el-table-column",{attrs:{label:"创建时间",sortable:"custom","show-overflow-tooltip":"","min-width":"160"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[e._v(e._s(e._f("toDateString")(1e3*o.create_time)))]}}],null,!0)}),o("el-table-column",{attrs:{label:"更新时间",sortable:"custom","show-overflow-tooltip":"","min-width":"160"},scopedSlots:e._u([{key:"default",fn:function(t){var o=t.row;return[e._v(e._s(e._f("toDateString")(1e3*o.update_time)))]}}],null,!0)}),o("el-table-column",{attrs:{label:"操作",width:"130px",align:"center",resizable:!1,fixed:"right"},scopedSlots:e._u([{key:"default",fn:function(t){var l=t.row;return[e.permission.includes("sys:document:edit")?o("el-link",{attrs:{icon:"el-icon-edit",type:"primary",underline:!1},on:{click:function(t){return e.edit(l)}}},[e._v("修改")]):e._e(),o("el-popconfirm",{staticClass:"ele-action",attrs:{title:"确定要删除此文档教程吗？"},on:{confirm:function(t){return e.remove(l)}}},[e.permission.includes("sys:document:delete")?o("el-link",{attrs:{slot:"reference",icon:"el-icon-delete",type:"danger",underline:!1},slot:"reference"},[e._v("删除")]):e._e()],1)]}}],null,!0)})]}}])})],1),o("el-dialog",{attrs:{title:(e.editForm.id,"修改文档教程"),visible:e.showEdit,width:"900px","destroy-on-close":!0,"lock-scroll":!1},on:{"update:visible":function(t){e.showEdit=t},closed:function(t){e.editForm={}}}},[o("el-form",{ref:"editForm",attrs:{model:e.editForm,rules:e.editRules,"label-width":"auto"}},[o("el-row",[o("el-col",{attrs:{sm:11}},[o("el-form-item",{staticStyle:{"margin-bottom":"20px"},attrs:{label:"通知标题：",prop:"title"}},[o("el-input",{attrs:{placeholder:"请输入通知标题",clearable:""},model:{value:e.editForm.title,callback:function(t){e.$set(e.editForm,"title",t)},expression:"editForm.title"}})],1)],1),o("el-col",{attrs:{sm:11}},[o("el-form-item",{attrs:{label:"阅读量：",prop:"browse"}},[o("el-input-number",{staticClass:"ele-fluid ele-text-left",attrs:{"controls-position":"right",min:0,placeholder:"请输入阅读量"},model:{value:e.editForm.browse,callback:function(t){e.$set(e.editForm,"browse",t)},expression:"editForm.browse"}})],1)],1)],1),o("el-row",[o("el-col",{attrs:{sm:11}},[o("el-form-item",{staticStyle:{"margin-bottom":"20px"},attrs:{label:"文档类型:",prop:"type"}},[o("el-select",{attrs:{placeholder:"请选择"},model:{value:e.editForm.type,callback:function(t){e.$set(e.editForm,"type",t)},expression:"editForm.type"}},e._l(e.options,(function(e){return o("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1)],1)],1),o("el-col",{attrs:{sm:6}},[o("el-form-item",{staticStyle:{"margin-left":"20px"},attrs:{label:"是否置顶：",prop:"is_top"}},[o("el-select",{attrs:{placeholder:"请选择"},model:{value:e.editForm.is_top,callback:function(t){e.$set(e.editForm,"is_top",t)},expression:"editForm.is_top"}},e._l(e.isoptions,(function(e){return o("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1)],1)],1),o("el-col",{attrs:{sm:5}},[o("el-form-item",{attrs:{label:"状态：",prop:"status"}},[o("el-select",{attrs:{placeholder:"请选择"},model:{value:e.editForm.status,callback:function(t){e.$set(e.editForm,"status",t)},expression:"editForm.status"}},e._l(e.isoptions,(function(e){return o("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1)],1)],1)],1),o("el-form-item",{attrs:{label:"通知内容：",prop:"content"}},[[o("quill-editor",{attrs:{options:e.editorOption},model:{value:e.editForm.content,callback:function(t){e.$set(e.editForm,"content",t)},expression:"editForm.content"}})]],2)],1),o("div",{attrs:{slot:"footer"},slot:"footer"},[o("el-button",{on:{click:function(t){e.showEdit=!1}}},[e._v("取消")]),o("el-button",{attrs:{type:"primary"},on:{click:e.save}},[e._v("保存")])],1)],1)],1)},s=[],r=(o("d81d"),o("5530")),a=o("2f62"),i=o("953d"),n=(o("a753"),o("8096"),o("14e1"),{name:"SysDocument",data:function(){return{table:{url:"/document/index",where:{}},choose:[],showEdit:!1,editForm:{},editRules:{title:[{required:!0,message:"请输入通知标题",trigger:"blur"}],content:[{required:!0,message:"请输入通知内容",trigger:"blur"}],is_top:[{required:!0,message:"请选择是否置顶",trigger:"blur"}],browse:[{required:!0,message:"请输入阅读量",trigger:"blur"}],status:[{required:!0,message:"请选择状态",trigger:"blur"}],by_id:[{required:!0,message:"请输入",trigger:"blur"}],type:[{required:!0,message:"选择文档类型",trigger:"blur"}]},options:[{value:1,label:"官方文档"},{value:2,label:"使用教程"}],isoptions:[{value:0,label:"否"},{value:1,label:"是"}],editorOption:{placeholder:"",modules:{toolbar:[["bold","italic","underline","strike"],["blockquote","code-block"],[{header:1},{header:2}],[{list:"ordered"},{list:"bullet"}],[{indent:"-1"},{indent:"+1"}],["image"]]}}}},computed:Object(r["a"])({},Object(a["b"])(["permission"])),mounted:function(){},components:{quillEditor:i["quillEditor"]},methods:{edit:function(e){var t=this;this.$http.get("/document/info?id="+e.id).then((function(e){0===e.data.code?(t.editForm=e.data.data,console.log(t.editForm),t.showEdit=!0):t.$message.error(e.data.msg)})).catch((function(e){t.$message.error(e.message)}))},save:function(){var e=this;this.$refs["editForm"].validate((function(t){if(!t)return!1;var o=e.$loading({lock:!0});e.$http.post("/document/edit",e.editForm).then((function(t){o.close(),0===t.data.code?(e.showEdit=!1,e.$message({type:"success",message:t.data.msg}),e.$refs.table.reload()):e.$message.error(t.data.msg)})).catch((function(t){o.close(),e.$message.error(t.message)}))}))},remove:function(e){var t=this;if(console.log(e),e){var o=this.$loading({lock:!0});this.$http.post("/document/delete",{id:e.id}).then((function(e){o.close(),0===e.data.code?(t.$message({type:"success",message:e.data.msg}),t.$refs.table.reload()):t.$message.error(e.data.msg)})).catch((function(e){o.close(),t.$message.error(e.message)}))}else{if(0===this.choose.length)return this.$message.error("请至少选择一条数据");var l=this.choose.map((function(e){return e.id}));this.$confirm("确定要删除选中的文档教程吗?","提示",{type:"warning"}).then((function(){var e=t.$loading({lock:!0});t.$http.post("/document/delete",{id:l}).then((function(o){e.close(),0===o.data.code?(t.$message({type:"success",message:o.data.msg}),t.$refs.table.reload()):t.$message.error(o.data.msg)})).catch((function(o){e.close(),t.$message.error(o.message)}))})).catch((function(){return 0}))}},setIsTop:function(e){var t=this,o=this.$loading({lock:!0}),l=Object.assign({},e);this.$http.post("/document/setIsTop",l).then((function(l){o.close(),0===l.data.code?t.$message({type:"success",message:l.data.msg}):(e.is_top=e.is_top?1:2,t.$message.error(l.data.msg))})).catch((function(e){o.close(),t.$message.error(e.message)}))},status:function(e){var t=this,o=this.$loading({lock:!0}),l=Object.assign({},e);this.$http.post("/document/status",l).then((function(l){o.close(),0===l.data.code?t.$message({type:"success",message:l.data.msg}):(e.status=e.status?1:2,t.$message.error(l.data.msg))})).catch((function(e){o.close(),t.$message.error(e.message)}))}}}),c=n,u=(o("42dc"),o("2877")),d=Object(u["a"])(c,l,s,!1,null,"20ed9f17",null);t["default"]=d.exports}}]);