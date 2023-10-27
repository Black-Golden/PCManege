<template>
	<div class="ele-body">
		<el-card shadow="never" style="position: relative;">
			<!-- 搜索表单 -->
			<el-form :model="table.where" label-width="77px" class="ele-form-search"
				@keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
				<el-row :gutter="15">
					<el-col :md="6" :sm="12">
						<el-form-item label="用户名:">
							<el-input v-model="table.where.user_name" placeholder="请输入用户名" clearable />
						</el-form-item>
					</el-col>
					<el-col :md="6" :sm="12">
						<el-form-item label="交易类型:">
							<el-select clearable v-model="table.where.type_id" placeholder="请选择交易类型">
								<el-option v-for="item in tradeType" :key="item.value" :label="item.label"
									:value="item.value">
								</el-option>
							</el-select>
						</el-form-item>
					</el-col>
					<el-col :md="9" :sm="19">
						<div class="block">
							<!-- <span class="demonstration">搜索时间</span> -->
							<el-form-item label="搜索时间">
								<el-date-picker @change="rq_change" value-format="yyyy-MM-dd HH:mm"
									style="margin-left:10px" v-model="create_time" type="datetimerange"
									start-placeholder="开始日期" end-placeholder="结束日期">
								</el-date-picker></el-form-item>
						</div>
					</el-col>
					<el-col :md="10" :sm="16">
						<div class="ele-form-actions">
						
							<el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search"
								class="ele-btn-icon">查询
							</el-button>
							
							<el-button @click=";(table.where = {}) && $refs.table.reload()">重置</el-button>
							<el-button type="success" @click="handleDownload()" icon="el-icon-download"
								class="ele-btn-icon">导出
							</el-button>
						</div>
					</el-col>
				</el-row>
			</el-form>
			<!-- 操作按钮 -->
			<div class="ele-table-tool ele-table-tool-default">
				<!--        <el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantwallet:add')">添加-->
				<!--        </el-button>-->
				<!--        <el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small" v-if="permission.includes('sys:quantwallet:dall')">批量删除-->
				<!--        </el-button>-->
			</div>
			<!-- 数据表格 -->
			<ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)"
				highlight-current-row>
				<template slot-scope="{ index }">
					<el-table-column type="selection" width="45" align="center" fixed="left" />
					<el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left"
						show-overflow-tooltip />

					<el-table-column prop="user_name" label="用户名" show-overflow-tooltip min-width="120" />

					<el-table-column prop="type_name" label="交易类型" show-overflow-tooltip min-width="120" />

					<el-table-column prop="num" label="金额" show-overflow-tooltip min-width="120">
						<template slot-scope="scope">
							<div :style="{ color: scope.row.num > 0 ? 'green' : 'red' }">
								{{ scope.row.num }}
							</div>
						</template>
					</el-table-column>
					<el-table-column prop="last_num" label="余额" show-overflow-tooltip min-width="120" />

					<el-table-column prop="memo" label="备注" show-overflow-tooltip min-width="120" />

					<el-table-column prop="create_time" label="创建时间" show-overflow-tooltip min-width="120" />
					<el-table-column prop="update_time" label="更新时间" show-overflow-tooltip min-width="120" />
					<!--          <el-table-column label="操作" width="130px" align="center" :resizable="false"  fixed="right">-->
					<!--            <template slot-scope="{row}">-->
					<!--&lt;!&ndash;              <el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false" v-if="permission.includes('sys:quantwallet:edit')">修改</el-link>&ndash;&gt;-->
					<!--              <el-popconfirm title="确定要删除此钱包奖励吗？" @confirm="remove(row)" class="ele-action">-->
					<!--                <el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false" v-if="permission.includes('sys:quantwallet:delete')">删除</el-link>-->
					<!--              </el-popconfirm>-->
					<!--            </template>-->
					<!--          </el-table-column>-->
				</template>
			</ele-data-table>
			<div class="collect"><span>汇总</span> {{ sum }}</div>
		</el-card>

		<!-- 编辑弹窗 -->
		<el-dialog :title="editForm.id ? '修改钱包奖励' : '修改钱包奖励'" :visible.sync="showEdit" width="450px"
			@closed="editForm = {}" :destroy-on-close="true" :lock-scroll="false">
			<el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
				<el-form-item label="：" prop="user_id">
					<el-input-number v-model="editForm.user_id" controls-position="right" :min="0" placeholder="请输入"
						class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="钱包类型：" prop="wallet">
					<el-input v-model="editForm.wallet" placeholder="请输入钱包类型" clearable />
				</el-form-item>

				<el-form-item label="奖励数量：" prop="num">
					<el-input-number v-model="editForm.num" controls-position="right" :min="0" placeholder="请输入奖励数量"
						class="ele-fluid ele-text-left" />
				</el-form-item>

				<el-form-item label="备注：" prop="memo">
					<el-input v-model="editForm.memo" placeholder="请输入备注" clearable />
				</el-form-item>
			</el-form>
			<div slot="footer">
				<el-button @click="showEdit = false">取消</el-button>
				<el-button type="primary" @click="save">保存</el-button>
			</div>
		</el-dialog>
	</div>
</template>

<script>
	import {
		mapGetters
	} from 'vuex'
	export default {
		name: 'SysQuantWallet',
		data() {
			return {
				export_list: [],
				count: "",
				create_time: '',
				sum: '',
				table: {
					url: '/quantwallet/index',
					where: {}
				}, // 表格配置
				choose: [], // 表格选中数据
				showEdit: false, // 是否显示表单弹窗
				editForm: {}, // 表单数据
				editRules: {
					// 表单验证规则

					user_id: [{
						required: true,
						message: '请输入',
						trigger: 'blur'
					}],

					wallet: [{
						required: true,
						message: '请输入钱包类型',
						trigger: 'blur'
					}, ],

					num: [{
						required: true,
						message: '请输入奖励数量',
						trigger: 'blur'
					}],

					memo: [{
						required: true,
						message: '请输入备注',
						trigger: 'blur'
					}],
				},
				// 交易类型
				tradeType: [{
						value: 1,
						label: '充值',
					},
					{
						value: 2,
						label: '提款',
					},

					{
						value: 5,
						label: '扣除手续费',
					},

				],
			}
		},
		computed: {
			...mapGetters(['permission']),
		},
		created() {
			this.getSum()
		},
		mounted() {
			this.account_details()
		},
		methods: {
			rq_change(e) {
				this.table.where.start_time = e[0]
				this.table.where.end_time = e[1]
			},
			getSum() {
				this.$http.get('/quantwallet/index').then((res) => {
					// this.sum = res.sum
					console.log(res.data)
					this.sum = res.data.sum
				})
			},
			/**
			 * 显示编辑
			 */
			edit(row) {
				this.$http
					.get('/quantwallet/info?id=' + row.id)
					.then((res) => {
						if (res.data.code === 0) {
							this.editForm = res.data.data
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
							.post('/quantwallet/edit', this.editForm)
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
					this.$confirm('确定要删除选中的钱包奖励吗?', '提示', {
							type: 'warning',
						})
						.then(() => {
							const loading = this.$loading({
								lock: true
							})
							this.$http
								.post('/quantwallet/delete', {
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
						.post('/quantwallet/delete', {
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
				this.$http.get('/quantwallet/index').then(res => {
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
				this.$http.post('/quantwallet/index', this.table.where).then(res => {
					if (res.data.code === 0) {
						this.export_list = res.data.data
						for (var i = 0; i < this.export_list.length; i++) {
							if (this.export_list[i].is_check == 1) {
								this.export_list[i].is_check = '已审核'
							} else if (this.export_list[i].is_check == 0) {
								this.export_list[i].is_check = '未审核'
							} else if (this.export_list[i].is_check == 2) {
								this.export_list[i].is_check = '已拒绝'
							}
							// this.export_list[i].is_check = getLocalTime(this.export_list[i]
							// 	.create_time);
							// this.export_list[i].update_time = getLocalTime(this.export_list[i]
							// 	.update_time);
						}
						console.log(this.export_list);
						import('@/vendor/Export2Excel').then(excel => {
							const header = [
								'用户名',
								'交易类型',
								'金额',
								'余额',
								'备注',
								'创建时间',
								'更新时间',


							]
							const filterVal = [
								'user_name',
								'type_name',
								'num',
								'last_num',
								'memo',
								'create_time',
								'update_time',

							]
							const list = this.export_list
							const filename = '资金流水'
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

	.collect {
		position: absolute;
		left: 1%;
		bottom: 2%;
		/* bottom: 50%; */
		padding: 10px;
		/* background-color: pink; */
	}

	.collect span {
		font-weight: 600;
	}
</style>