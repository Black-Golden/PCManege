<template>
	<div class="ele-body">
		<el-card shadow="never">
			<!-- 搜索表单 -->
			<el-form :model="table.where" label-width="77px" class="ele-form-search"
				@keyup.enter.native="$refs.table.reload()" @submit.native.prevent>
				<el-row :gutter="15">

					<el-col :md="6" :sm="12">
						<el-form-item label="策略名称:">
							<el-input v-model="table.where.name" placeholder="请输入策略名称" clearable />
						</el-form-item>
					</el-col>
					<el-col :md="6" :sm="12">
						<div class="ele-form-actions">
							<el-button type="primary" @click="$refs.table.reload()" icon="el-icon-search"
								class="ele-btn-icon">查询
							</el-button>
							<el-button @click="(table.where={})&&$refs.table.reload()">重置</el-button>
						</div>
					</el-col>
				</el-row>
			</el-form>
			<!-- 操作按钮 -->
			<div class="ele-table-tool ele-table-tool-default">
				<el-button @click="showEdit=true" type="primary" icon="el-icon-plus" class="ele-btn-icon" size="small"
					v-if="permission.includes('sys:product:add')">添加
				</el-button>
				<el-button @click="remove()" type="danger" icon="el-icon-delete" class="ele-btn-icon" size="small"
					v-if="permission.includes('sys:product:dall')">批量删除
				</el-button>
			</div>
			<!-- 数据表格 -->
			<ele-data-table ref="table" :config="table" :choose.sync="choose" height="calc(100vh - 315px)"
				highlight-current-row>
				<template slot-scope="{index}">
					<el-table-column type="selection" width="45" align="center" fixed="left" />
					<el-table-column type="index" :index="index" label="编号" width="60" align="center" fixed="left"
						show-overflow-tooltip />

					<el-table-column prop="name" label="策略名称" sortable="custom" show-overflow-tooltip min-width="120" />

					<el-table-column prop="expected_return" label="预期收益率" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="chance_winning" label="胜率" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="withdraw" label="最大撤回率" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="sharpe_ratio" label="夏普比率" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="control_line" label="控制线" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="lever" label="杠杆倍数" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="min_amount" label="最小投资额" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="policy_capacity" label="策略容量" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="tradable_assets" label="可交易资产" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="symbol" label="交易币种" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="share_ratio" label="分成比例" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="into_periods" label="分成周期" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="protocol_period" label="协议周期" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<el-table-column prop="platform_id" label="托管平台" sortable="custom" show-overflow-tooltip
						min-width="120" />

					<!--          <el-table-column prop="break_even" label="保本" sortable="custom" show-overflow-tooltip min-width="120"/>-->

					<el-table-column label="创建时间" sortable="custom" show-overflow-tooltip min-width="160">
						<template slot-scope="{row}">{{ row.create_time*1000 | toDateString }}</template>
					</el-table-column>
					<el-table-column label="更新时间" sortable="custom" show-overflow-tooltip min-width="160">
						<template slot-scope="{row}">{{ row.update_time*1000 | toDateString }}</template>
					</el-table-column>
					<el-table-column label="操作" width="130px" align="center" :resizable="false" fixed="right">
						<template slot-scope="{row}">
							<el-link @click="edit(row)" icon="el-icon-edit" type="primary" :underline="false"
								v-if="permission.includes('sys:product:edit')">修改</el-link>
							<el-popconfirm title="确定要删除此产品列吗？" @confirm="remove(row)" class="ele-action">
								<el-link slot="reference" icon="el-icon-delete" type="danger" :underline="false"
									v-if="permission.includes('sys:product:delete')">删除</el-link>
							</el-popconfirm>
						</template>
					</el-table-column>
				</template>
			</ele-data-table>
		</el-card>
		<!-- 编辑弹窗 -->
		<el-dialog :title="editForm.id?'修改产品列':'修改产品列'" :visible.sync="showEdit" width="800px" @closed="editForm={}"
			:destroy-on-close="true" :lock-scroll="false">
			<el-form :model="editForm" ref="editForm" :rules="editRules" label-width="100px">
				<el-row :gutter="15">
					<el-col :sm="12">
						<el-form-item label="策略名称:" prop="name" label-width="120px">
							<el-input v-model="editForm.name" placeholder="请输入策略名称" clearable />
						</el-form-item>

						<el-form-item label="预期收益率:" prop="expected_return" label-width="120px">
							<el-input v-model="editForm.expected_return" placeholder="请输入预期收益率" clearable />
						</el-form-item>

						<el-form-item label="胜率:" prop="chance_winning" label-width="120px">
							<el-input v-model="editForm.chance_winning" placeholder="请输入胜率" clearable />
						</el-form-item>

						<el-form-item label="最大撤回率:" prop="withdraw" label-width="120px">
							<el-input v-model="editForm.withdraw" placeholder="请输入最大撤回率" clearable />
						</el-form-item>

						<el-form-item label="夏普比率:" prop="sharpe_ratio" label-width="120px">
							<el-input v-model="editForm.sharpe_ratio" placeholder="请输入夏普比率" clearable />
						</el-form-item>

						<el-form-item label="控制线:" prop="control_line" label-width="120px">
							<el-input v-model="editForm.control_line" placeholder="请输入控制线" clearable />
						</el-form-item>

						<el-form-item label="杠杆倍数:" prop="lever" label-width="120px">
							<el-input-number v-model="editForm.lever" controls-position="right" :min="0"
								placeholder="请输入杠杆倍数" class="ele-fluid ele-text-left" />
						</el-form-item>


						<el-form-item label="最小投资额:" prop="min_amount" label-width="120px">
							<el-input v-model="editForm.min_amount" placeholder="请输入最小投资额" clearable />
						</el-form-item>

					</el-col>
					<el-col :sm="12">
						<el-form-item label="策略容量:" prop="policy_capacity" label-width="120px">
							<el-input v-model="editForm.policy_capacity" placeholder="请输入策略容量" clearable />
						</el-form-item>

						<el-form-item label="可交易资产:" prop="tradable_assets" label-width="120px">
							<el-input v-model="editForm.tradable_assets" placeholder="请输入可交易资产" clearable />
						</el-form-item>

						<el-form-item label="交易币种:" prop="symbol" label-width="120px">
							<el-input v-model="editForm.symbol" placeholder="请输入交易币种" clearable />
						</el-form-item>

						<el-form-item label="分成比例:" prop="share_ratio" label-width="120px">
							<el-input v-model="editForm.share_ratio" placeholder="请输入分成比例" clearable />
						</el-form-item>

						<el-form-item label="分成周期:" prop="into_periods" label-width="120px">
							<el-input v-model="editForm.into_periods" placeholder="请输入分成周期" clearable />
						</el-form-item>

						<el-form-item label="协议周期:" prop="protocol_period" label-width="120px">
							<el-input v-model="editForm.protocol_period" placeholder="请输入协议周期" clearable />
						</el-form-item>


						<el-form-item label="托管平台:" prop="platform_id" label-width="120px">
							<el-select v-model="editForm.platform_id" placeholder="请选择托管平台" class="ele-block">
								<el-option v-for="item in yyslist" :label="item.name" :value="item.id">
								</el-option>
							</el-select>

						</el-form-item>
						<!-- 	<el-input-number v-model="editForm.platform_id" controls-position="right" :min="0"
								placeholder="请输入托管平台" class="ele-fluid ele-text-left" /> -->

						<!--                                                      -->
						<!--        <el-form-item label="保本：" prop="break_even" label-width="120px">-->
						<!--            <el-input v-model="editForm.break_even" placeholder="请输入保本" clearable/>-->
						<!--        </el-form-item>-->
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer">
				<el-button @click="showEdit=false">取消</el-button>
				<el-button type="primary" @click="save">保存</el-button>
			</div>
		</el-dialog>
	</div>
</template>

<script>
	import {
		mapGetters
	} from "vuex";
	export default {
		name: "SysProduct",
		data() {
			return {
				yyslist: [{
					id: 2,
					name: "欧易"
				}, {
					id: 3,
					name: "币安"
				}],
				table: {
					url: '/product/index',
					where: {}
				}, // 表格配置
				choose: [], // 表格选中数据
				showEdit: false, // 是否显示表单弹窗
				editForm: {}, // 表单数据
				editRules: { // 表单验证规则

					name: [{
						required: true,
						message: '请输入策略名称',
						trigger: 'blur'
					}],

					expected_return: [{
						required: true,
						message: '请输入预期收益率',
						trigger: 'blur'
					}],

					chance_winning: [{
						required: true,
						message: '请输入胜率',
						trigger: 'blur'
					}],

					withdraw: [{
						required: true,
						message: '请输入最大撤回率',
						trigger: 'blur'
					}],

					sharpe_ratio: [{
						required: true,
						message: '请输入夏普比率',
						trigger: 'blur'
					}],

					control_line: [{
						required: true,
						message: '请输入控制线',
						trigger: 'blur'
					}],

					lever: [{
						required: true,
						message: '请输入杠杆倍数',
						trigger: 'blur'
					}],

					min_amount: [{
						required: true,
						message: '请输入最小投资额',
						trigger: 'blur'
					}],

					policy_capacity: [{
						required: true,
						message: '请输入策略容量',
						trigger: 'blur'
					}],

					tradable_assets: [{
						required: true,
						message: '请输入可交易资产',
						trigger: 'blur'
					}],

					symbol: [{
						required: true,
						message: '请输入交易币种',
						trigger: 'blur'
					}],

					share_ratio: [{
						required: true,
						message: '请输入分成比例',
						trigger: 'blur'
					}],

					into_periods: [{
						required: true,
						message: '请输入分成周期',
						trigger: 'blur'
					}],

					protocol_period: [{
						required: true,
						message: '请输入协议周期',
						trigger: 'blur'
					}],

					platform_id: [{
						required: true,
						message: '请输入托管平台',
						trigger: 'blur'
					}],

					break_even: [{
						required: true,
						message: '请输入保本',
						trigger: 'blur'
					}],

				},
			}
		},
		computed: {
			...mapGetters(["permission"]),
		},
		mounted() {},
		methods: {
			/**
			 * 显示编辑
			 */
			edit(row) {
				this.$http.get('/product/info?id=' + row.id).then(res => {
					if (res.data.code === 0) {
						this.editForm = res.data.data;
						this.showEdit = true;
					} else {
						this.$message.error(res.data.msg);
					}
				}).catch(e => {
					this.$message.error(e.message);
				});
			},
			/**
			 * 保存编辑
			 */
			save() {
				this.$refs['editForm'].validate((valid) => {
					if (valid) {
						const loading = this.$loading({
							lock: true
						});
						this.$http.post('/product/edit', this.editForm).then(res => {
							loading.close();
							if (res.data.code === 0) {
								this.showEdit = false;
								this.$message({
									type: 'success',
									message: res.data.msg
								});
								this.$refs.table.reload();
							} else {
								this.$message.error(res.data.msg);
							}
						}).catch(e => {
							loading.close();
							this.$message.error(e.message);
						});
					} else {
						return false;
					}
				});
			},
			/**
			 * 刪除(批量刪除)
			 */
			remove(row) {
				console.log(row)
				if (!row) { // 批量删除
					if (this.choose.length === 0) return this.$message.error('请至少选择一条数据');
					let ids = this.choose.map(d => d.id);
					this.$confirm('确定要删除选中的产品列吗?', '提示', {
						type: 'warning'
					}).then(() => {
						const loading = this.$loading({
							lock: true
						});
						this.$http.post('/product/delete', {
							id: ids
						}).then(res => {
							loading.close();
							if (res.data.code === 0) {
								this.$message({
									type: 'success',
									message: res.data.msg
								});
								this.$refs.table.reload();
							} else {
								this.$message.error(res.data.msg);
							}
						}).catch(e => {
							loading.close();
							this.$message.error(e.message);
						});
					}).catch(() => 0);
				} else { // 单个删除
					const loading = this.$loading({
						lock: true
					});
					this.$http.post('/product/delete', {
						id: row.id
					}).then(res => {
						loading.close();
						if (res.data.code === 0) {
							this.$message({
								type: 'success',
								message: res.data.msg
							});
							this.$refs.table.reload();
						} else {
							this.$message.error(res.data.msg);
						}
					}).catch(e => {
						loading.close();
						this.$message.error(e.message);
					});
				}
			},
		}
	}
</script>

<style scoped>
	.ele-block>>>.el-upload,
	.ele-block>>>.el-upload-dragger {
		width: 100%;
	}
</style>