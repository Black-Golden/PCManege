<template>
	<div class="ele-body">
		<el-card shadow="never">
			<!-- 搜索表单 -->
			<el-form :model="table.where" label-width="77px" class="ele-form-search"
				@keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
				<el-row :gutter="15">
					<el-col :md="7" :sm="12">
						<el-form-item label="用户名:">
							<el-input v-model="table.where.user_name" placeholder="用户名" clearable />
						</el-form-item>
					</el-col>
					<el-col :md="7" :sm="12">
						<el-form-item label="代币种类:">
							<el-input v-model="table.where.symbol" placeholder="请输入代币种类" clearable />
						</el-form-item>
					</el-col>
					<el-col :md="7" :sm="12">
						<el-form-item label="所属平台:">
							<el-select clearable v-model="table.where.platform_id" placeholder="请选择所属平台">
								<el-option v-for="item in platformoptions" :key="item.value" :label="item.label"
									:value="item.value">
								</el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>

				<el-row :gutter="15">
					<el-col :md="9" :sm="19">
						<div class="block">
							<!-- <span class="demonstration">搜索时间</span> -->
							<el-form-item label="搜索时间">
								<el-date-picker @change="rq_change" value-format="yyyy-MM-dd HH:mm"
									style="margin-left:10px" v-model="create_time" type="datetimerange"
									start-placeholder="开始日期" end-placeholder="结束日期">
								</el-date-picker>
							</el-form-item>
						</div>
					</el-col>
					<el-col :md="6" :sm="12">
						<el-form-item label="开仓方向:">
							<el-select clearable v-model="table.where.open_type" placeholder="请选择开仓方向">
								<el-option v-for="item in directionoptions" :key="item.value" :label="item.label"
									:value="item.value">
								</el-option>
							</el-select>
						</el-form-item>
					</el-col>
					<el-col :md="6" :sm="12">
						<el-form-item label="买卖类型:">
							<el-select clearable v-model="table.where.type" placeholder="请选择买卖类型">
								<el-option v-for="item in dealType" :key="item.value" :label="item.label"
									:value="item.value">
								</el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row>
					<el-col :md="5" :sm="12">

						<div class="ele-form-actions">
							<el-button type="success" @click="handleDownload()" icon="el-icon-download"
								class="ele-btn-icon">导出
							</el-button>
							<el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search"
								class="ele-btn-icon">查询
							</el-button>
							<el-button @click=";(table.where = {}) && $refs.table.reload()">重置</el-button>
						</div>
					</el-col>
				</el-row>
			</el-form>
			<!-- 操作按钮 -->
			<!-- <div class="ele-table-tool ele-table-tool-default">
        <el-button
          @click="showEdit = true"
          type="primary"
          icon="el-icon-plus"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:tradelog:add')"
          >添加
        </el-button>
        <el-button
          @click="remove()"
          type="danger"
          icon="el-icon-delete"
          class="ele-btn-icon"
          size="small"
          v-if="permission.includes('sys:tradelog:dall')"
          >批量删除
        </el-button>
      </div> -->
			<!-- 数据表格 -->
			<ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)"
				highlight-current-row>
				<template slot-scope="{ index }">
					<el-table-column type="selection" width="45" align="center" fixed="left" />
					<el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left"
						show-overflow-tooltip />

					<el-table-column prop="content" label="日志内容" show-overflow-tooltip min-width="300" />

					<el-table-column prop="user_name" label="用户名" show-overflow-tooltip min-width="120" />

					<el-table-column prop="symbol" label="代币种类" show-overflow-tooltip min-width="120" />

					<el-table-column prop="platform_id" label="交易所类型" show-overflow-tooltip min-width="120">
						<template slot-scope="{ row }">
							<div>{{ row.platform_id == 2 ? '欧易' : '币安' }}</div>
						</template>
					</el-table-column>

					<el-table-column prop="open_type" label="开仓方向" show-overflow-tooltip min-width="120">
						<template slot-scope="{ row }">
							<div :style="{ color: row.open_type == 2 ? 'green' : 'red' }">
								{{ row.open_type == 2 ? '做多' : '做空' }}
							</div>
						</template>
					</el-table-column>

					<el-table-column prop="type" label="买卖类型" show-overflow-tooltip min-width="120">
						<template slot-scope="{ row }">
							<div :style="{ color: row.type == 2 ? 'green' : 'red' }">
								{{ row.type == 1 ? '买' : row.type == 2 ? '卖' : '普通' }}
							</div>
						</template>
					</el-table-column>

					<el-table-column prop="create_time" label="创建时间" show-overflow-tooltip min-width="160" />

					<el-table-column prop="update_time" label="更新时间" show-overflow-tooltip min-width="160" />
					<!-- <el-table-column
            label="操作"
            width="130px"
            align="center"
            :resizable="false"
            fixed="right"
          >
            <template slot-scope="{ row }">
              <el-link
                @click="edit(row)"
                icon="el-icon-view"
                type="primary"
                :underline="false"
                v-if="permission.includes('sys:tradelog:edit')"
                >查看</el-link
              >
              <el-popconfirm
                title="确定要删除此交易日志吗？"
                @confirm="remove(row)"
                class="ele-action"
              >
                <el-link
                  slot="reference"
                  icon="el-icon-delete"
                  type="danger"
                  :underline="false"
                  v-if="permission.includes('sys:tradelog:delete')"
                  >删除</el-link
                >
              </el-popconfirm>
            </template>
          </el-table-column> -->
				</template>
			</ele-data-table>
		</el-card>
		<!-- 编辑弹窗 -->
		<el-dialog :title="editForm.id ? '查看交易日志' : '新增交易日志'" :visible.sync="showEdit" width="550px"
			@closed="editForm = {}" :destroy-on-close="true" :lock-scroll="false">
			<el-form :model="editForm" ref="editForm" :rules="editRules" label-width="auto">
				<el-form-item label="日志内容：" prop="content">
					<el-input disabled v-model="editForm.content" placeholder="请输入日志内容"
						class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="用户昵称：" prop="user_name">
					<el-input disabled v-model="editForm.user_name" placeholder="请输入用户昵称"
						class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="代币种类：" prop="symbol">
					<el-input disabled v-model="editForm.symbol" placeholder="请输入代币种类" clearable />
				</el-form-item>

				<el-form-item label="交易所类型：" prop="platform_id">
					<el-select disabled v-model="editForm.platform_id" placeholder="请选择">
						<el-option v-for="item in platformoptions" :key="item.value" :label="item.label"
							:value="item.value">
						</el-option>
					</el-select>
				</el-form-item>

				<el-form-item label="开仓方向：" prop="open_type">
					<el-select disabled v-model="editForm.open_type" placeholder="请选择">
						<el-option v-for="item in directionoptions" :key="item.value" :label="item.label"
							:value="item.value">
						</el-option>
					</el-select>
				</el-form-item>

				<el-form-item label="买卖类型:" prop="type">
					<el-select disabled v-model="editForm.type" placeholder="请选择">
						<el-option v-for="item in dealType" :key="item.value" :label="item.label" :value="item.value">
						</el-option>
					</el-select>
				</el-form-item>
			</el-form>
			<div slot="footer">
				<el-button type="primary" @click="showEdit = false">关闭</el-button>
				<!-- <el-button type="primary" @click="save">保存</el-button> -->
			</div>
		</el-dialog>
	</div>
</template>

<script>
	import {
		mapGetters
	} from 'vuex'
	export default {
		name: 'SysTradeLog',
		data() {
			return {
				export_list: [],
				count: "",
				create_time: '',
				table: {
					url: '/tradelog/index',
					where: {}
				}, // 表格配置
				choose: [], // 表格选中数据
				showEdit: false, // 是否显示表单弹窗
				editForm: {}, // 表单数据
				editRules: {
					// 表单验证规则

					content: [{
						required: true,
						message: '请输入日志内容',
						trigger: 'blur'
					}, ],

					user_id: [{
						required: true,
						message: '请输入管理用户',
						trigger: 'blur'
					}, ],

					symbol: [{
						required: true,
						message: '请输入代币种类',
						trigger: 'blur'
					}, ],

					platform_id: [{
						required: true,
						message: '请输入交易所类型',
						trigger: 'blur'
					}, ],

					open_type: [{
						required: true,
						message: '请输入开仓方向',
						trigger: 'blur'
					}, ],

					type: [{
						required: true,
						message: '请输入买卖类型 1=买 2=卖 0是普通',
						trigger: 'blur',
					}, ],
				},
				// 买卖类型
				dealType: [{
						value: 1,
						label: '买',
					},
					{
						value: 2,
						label: '卖',
					},
					{
						value: 0,
						label: '普通',
					},
				],
				// 开仓方向
				directionoptions: [{
						value: 2,
						label: '做多',
					},
					{
						value: 3,
						label: '做空',
					},
				],
				// 所属平台
				platformoptions: [{
						value: 2,
						label: '欧易',
					},
					{
						value: 3,
						label: '币安',
					},
				],
			}
		},
		computed: {
			...mapGetters(['permission']),
		},
		mounted() {
			this.account_details()
		},
		methods: {
			rq_change(e) {
				this.table.where.start_time = e[0]
				this.table.where.end_time = e[1]
			},
			/**
			 * 显示编辑
			 */
			edit(row) {
				this.$http
					.get('/tradelog/info?id=' + row.id)
					.then((res) => {
						if (res.data.code === 0) {
							this.editForm = res.data.data
							// console.table(this.editForm)
							this.showEdit = true
						} else {
							this.$message.error(res.data.msg)
						}
					})
					.catch((e) => {
						this.$message.error(e.message)
					})
			},
			/**
			 * 保存编辑
			 */
			save() {
				this.$refs['editForm'].validate((valid) => {
					if (valid) {
						const loading = this.$loading({
							lock: true
						})
						this.$http
							.post('/tradelog/edit', this.editForm)
							.then((res) => {
								loading.close()
								if (res.data.code === 0) {
									this.showEdit = false
									this.$message({
										type: 'success',
										message: res.data.msg
									})
									this.$refs.table.reload()
								} else {
									this.$message.error(res.data.msg)
								}
							})
							.catch((e) => {
								loading.close()
								this.$message.error(e.message)
							})
					} else {
						return false
					}
				})
			},
			/**
			 * 刪除(批量刪除)
			 */
			remove(row) {
				console.log(row)
				if (!row) {
					// 批量删除
					if (this.choose.length === 0)
						return this.$message.error('请至少选择一条数据')
					let ids = this.choose.map((d) => d.id)
					this.$confirm('确定删除选中的交易日志吗?', '提示', {
							type: 'warning',
						})
						.then(() => {
							const loading = this.$loading({
								lock: true
							})
							this.$http
								.post('/tradelog/delete', {
									id: ids
								})
								.then((res) => {
									loading.close()
									if (res.data.code === 0) {
										this.$message({
											type: 'success',
											message: res.data.msg
										})
										this.$refs.table.reload()
									} else {
										this.$message.error(res.data.msg)
									}
								})
								.catch((e) => {
									loading.close()
									this.$message.error(e.message)
								})
						})
						.catch(() => 0)
				} else {
					// 单个删除
					const loading = this.$loading({
						lock: true
					})
					this.$http
						.post('/tradelog/delete', {
							id: row.id
						})
						.then((res) => {
							loading.close()
							if (res.data.code === 0) {
								this.$message({
									type: 'success',
									message: res.data.msg
								})
								this.$refs.table.reload()
							} else {
								this.$message.error(res.data.msg)
							}
						})
						.catch((e) => {
							loading.close()
							this.$message.error(e.message)
						})
				}
			},
			account_details() {
				this.$http.get('/tradelog/index').then(res => {
					if (res.data.code === 0) {
						this.count = res.data.count
						console.log(this.count)
					} else {
						this.$message.error(res.data.msg);
					}
				}).catch(e => {
					this.$message.error(e.message);
				});

			},
			handleDownload(index) {
				this.table.where.limit = this.count
				this.table.where.page = 1
				this.$http.post('/tradelog/index', this.table.where).then(res => {
					if (res.data.code === 0) {
						this.export_list = res.data.data
						for (var i = 0; i < this.export_list.length; i++) {
							if (this.export_list[i].platform_id == 2) {
								this.export_list[i].platform_id = '欧易'
							} else {
								this.export_list[i].platform_id = '币安'
							}
							if (this.export_list[i].open_type == 2) {
								this.export_list[i].open_type = '做多'
							} else {
								this.export_list[i].open_type = '做空'
							}
							if (this.export_list[i].type == 1) {
								this.export_list[i].type = '买'
							} else if (this.export_list[i].type == 2) {
								this.export_list[i].type = '卖'
							}else {
								this.export_list[i].type = '普通'
							}
							
						}
						console.log(this.export_list);
						import('@/vendor/Export2Excel').then(excel => {
							const header = [
								'日志内容',
								'用户名',
								'代币种类',
								'交易所类型',
								'开仓方向',
								'买卖类型',

								'创建时间',
								'更新时间'


							]
							const filterVal = [
								'content',
								'user_name',
								'symbol',
								
								'platform_id',
								"open_type",
								"type",



								'create_time',
								'update_time',


							]
							const list = this.export_list
							const filename = '交易日志'
							const data = this.formatJson(filterVal, list)
							excel.export_json_to_excel({
								header,
								data,
								filename
							})
							this.downloadLoading = false
							this.downloadLoading1 = false
						})

					} else {
						this.$message.error(res.data.msg);
					}
				}).catch(e => {
					this.$message.error(e.message);
				});
			},
			formatJson(filterVal, jsonData) {
				return jsonData.map(v => filterVal.map(j => {
					if (j === 'timestamp') {
						return parseTime(v[j])
					} else {
						return v[j]
					}
				}))
			},
		},
	}

	function getLocalTime(nS) {
		return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/, ' ');
	}
</script>

<style scoped>
	.ele-block>>>.el-upload,
	.ele-block>>>.el-upload-dragger {
		width: 100%;
	}
</style>