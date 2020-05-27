<template>
    <div class="page">
        <el-steps :active="activeStep" finish-status="success" simple class="book-process">
            <el-step title="乘车信息"></el-step>
            <el-step title="付款"></el-step>
            <el-step title="出票"></el-step>
        </el-steps>
        <div v-loading="isTrainLoading">
            <div v-if="activeStep == 0" class="step-0">
                <el-card class="info-card" shadow="never">
                    <div slot="header" class="clearfix">
                        <span>列车信息</span>
                    </div>
                    <div class="content">
                        <div class="basic-info">
                            <span style="font-size: 20px; font-weight: 500;">{{date}}</span>
                            <span style="font-size: 20px; font-weight: 500; margin-right: 5px;">（{{parseDay($moment(date).day())}}）</span>
                            <span style="font-size: 20px; font-weight: 500;">{{train.numbers ? JSON.parse(train.numbers)[0] : ''}}</span>
                            <span>次</span>
                            <span style="font-size: 20px; font-weight: 500;">{{departStation.name}}</span>
                            <span>站</span>
                            <span style="font-size: 20px; font-weight: 500;">（{{departStation.pivot?$moment(departStation.pivot.depart_time, "HH:mm:ss").format('HH:mm'):''}}开）</span>
                            <span style="font-size: 20px; font-weight: 500;"><i class="far fa-long-arrow-alt-right"></i></span>
                            <span style="font-size: 20px; font-weight: 500;">{{arriveStation.name}}</span>
                            <span>站</span>
                            <span>（{{arriveStation.pivot?$moment(arriveStation.pivot.arrive_time, "HH:mm:ss").format('HH:mm'):''}}到）</span>
                        </div>
                        <div class="seat-info" v-if="train.numbers && (JSON.parse(train.numbers)[0][0] == 'G' || JSON.parse(train.numbers)[0][0] == 'D')">
                            <span style="margin-right: 15px;">商务座（<span style="color: #67c23a;">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['SZ']:''}}</span>）{{train.seats_availability['SZ']}}张票</span>
                            <span style="margin-right: 15px;">一等座（<span style="color: #67c23a;">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['1Z']:''}}</span>）{{train.seats_availability['1Z']}}张票</span>
                            <span style="margin-right: 15px;">二等座（<span style="color: #67c23a;">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['2Z']:''}}</span>）{{train.seats_availability['2Z']}}张票</span>
                        </div>
                        <div class="seat-info" v-else-if="train.numbers && JSON.parse(train.numbers)[0][0] == 'C'">
                            <span style="margin-right: 15px;">一等座（<span style="color: #67c23a;">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['1Z']:''}}</span>）{{train.seats_availability['1Z']}}张票</span>
                            <span style="margin-right: 15px;">二等座（<span style="color: #67c23a;">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['2Z']:''}}</span>）{{train.seats_availability['2Z']}}张票</span>
                        </div>
                        <div class="seat-info" v-else-if="train.numbers">
                            <span style="margin-right: 15px;">硬座（<span style="color: #67c23a;">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['YZ']:''}}</span>）{{train.seats_availability['YZ']}}张票</span>
                            <span style="margin-right: 15px;">软卧（<span style="color: #67c23a;">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['RW']:''}}</span>）{{train.seats_availability['RW']}}张票</span>
                            <span style="margin-right: 15px;">硬卧（<span style="color: #67c23a;">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['YW']:''}}</span>）{{train.seats_availability['YW']}}张票</span>
                        </div>
                    </div>
                </el-card>
                <el-card class="passenger-card" shadow="never">
                    <div slot="header" class="clearfix">
                        <span>乘客信息</span>
                    </div>
                    <div class="content">
                        <el-form :inline="true" ref="passengerForm" :model="passengerForm" :rules="passengerRules" class="passenger-form">
                            <el-form-item label="乘客" prop="passengerId">
                                <el-select v-model="passengerForm.passengerId" placeholder="请选择乘客">
                                    <el-option v-for="passenger in passengers" :key="passenger.id" :label="passenger.name" :value="passenger.id">
                                        <span style="float: left">{{ passenger.name }}</span>
                                        <el-tag style="float: right; margin: 6.5px 0;" :type="passenger.type_name[1]" size="mini">{{passenger.type_name[0]}}</el-tag>
                                    </el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="席位" prop="seatType">
                                <el-select v-model="passengerForm.seatType" placeholder="请选择席位" v-if="train.numbers && (JSON.parse(train.numbers)[0][0] == 'G' || JSON.parse(train.numbers)[0][0] == 'D')">
                                    <el-option label="商务座" value="SZ" :disabled="train.seats_availability['SZ'] <= 0">
                                        <span style="float: left">商务座</span>
                                        <span style="float: right; color: #8492a6; font-size: 13px">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['SZ']:''}}</span>
                                    </el-option>
                                    <el-option label="一等座" value="1Z" :disabled="train.seats_availability['1Z'] <= 0">
                                        <span style="float: left">一等座</span>
                                        <span style="float: right; color: #8492a6; font-size: 13px">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['1Z']:''}}</span>
                                    </el-option>
                                    <el-option label="二等座" value="2Z" :disabled="train.seats_availability['2Z'] <= 0">
                                        <span style="float: left">二等座</span>
                                        <span style="float: right; color: #8492a6; font-size: 13px">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['2Z']:''}}</span>
                                    </el-option>
                                </el-select>
                                <el-select v-model="passengerForm.seatType" placeholder="请选择席位" v-else-if="train.numbers && JSON.parse(train.numbers)[0][0] == 'C'">
                                    <el-option label="一等座" value="1Z" :disabled="train.seats_availability['1Z'] <= 0">
                                        <span style="float: left">一等座</span>
                                        <span style="float: right; color: #8492a6; font-size: 13px">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['1Z']:''}}</span>
                                    </el-option>
                                    <el-option label="二等座" value="2Z" :disabled="train.seats_availability['2Z'] <= 0">
                                        <span style="float: left">二等座</span>
                                        <span style="float: right; color: #8492a6; font-size: 13px">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['2Z']:''}}</span>
                                    </el-option>
                                </el-select>
                                <el-select v-model="passengerForm.seatType" placeholder="请选择席位" v-else-if="train.numbers">
                                    <el-option label="硬座" value="YZ" :disabled="train.seats_availability['YZ'] <= 0">
                                        <span style="float: left">硬座</span>
                                        <span style="float: right; color: #8492a6; font-size: 13px">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['YZ']:''}}</span>
                                    </el-option>
                                    <el-option label="软卧" value="RW" :disabled="train.seats_availability['RW'] <= 0">
                                        <span style="float: left">软卧</span>
                                        <span style="float: right; color: #8492a6; font-size: 13px">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['RW']:''}}</span>
                                    </el-option>
                                    <el-option label="硬卧" value="YW" :disabled="train.seats_availability['YW'] <= 0">
                                        <span style="float: left">硬卧</span>
                                        <span style="float: right; color: #8492a6; font-size: 13px">¥{{departStation.pivot?train.price[departStation.pivot.station_num][arriveStation.pivot.station_num]['YW']:''}}</span>
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </el-form>
                    </div>
                </el-card>
                <div class="operation-btns">
                    <el-button @click="router.push('/')" :disabled="isOrderSubmitting">取消操作</el-button>
                    <el-button type="primary" @click="submitOrder" :loading="isOrderSubmitting">提交订单</el-button>
                </div>
            </div>
            <div v-else-if="activeStep == 1" class="step-1">
                <el-card class="info-card" shadow="never">
                    <div slot="header" class="clearfix">
                        <span>列车信息</span>
                    </div>
                    <div class="content">
                        <div class="basic-info">
                            <div class="first">
                                <span style="font-size: 20px; font-weight: 500;">{{date}}</span>
                                <span style="font-size: 20px; font-weight: 500; margin-right: 5px;">（{{parseDay($moment(date).day())}}）</span>
                                <span style="font-size: 20px; font-weight: 500;">{{train.numbers ? JSON.parse(train.numbers)[0] : ''}}</span>
                                <span>次</span>
                            </div>
                            <div class="second">
                                <span style="font-size: 20px; font-weight: 500;">{{departStation.name}}</span>
                                <span>站</span>
                                <span style="font-size: 20px; font-weight: 500;">（{{departStation.pivot?$moment(departStation.pivot.depart_time, "HH:mm:ss").format('HH:mm'):''}}开）</span>
                                <span style="font-size: 20px; font-weight: 500;"><i class="far fa-long-arrow-alt-right"></i></span>
                                <span style="font-size: 20px; font-weight: 500;">{{arriveStation.name}}</span>
                                <span>站</span>
                                <span>（{{arriveStation.pivot?$moment(arriveStation.pivot.arrive_time, "HH:mm:ss").format('HH:mm'):''}}到）</span>
                            </div>
                        </div>
                    </div>
                </el-card>
                <el-card class="passenger-card" shadow="never">
                    <div slot="header" class="clearfix">
                        <span style="display: unset;">车票信息</span>
                        <el-tag style="float: right; margin-top: -3.5px; font-size: 13px;" type="danger" size="medium">未支付</el-tag>
                    </div>
                    <div class="content">
                        <span>乘客：<span style="font-weight: 500; display: unset;">{{passenger.name}}</span></span>
                        <span>车厢：<span style="font-weight: 500; display: unset;">{{ticket.carriage}}</span></span>
                        <span>席位：<span style="font-weight: 500; display: unset;">{{ticket.seat_type == 'SZ' ? '商务座' : (ticket.seat_type == '1Z' ? '一等座' : (ticket.seat_type == '2Z' ? '二等座' : (ticket.seat_type == 'YZ' ? '硬座' : (ticket.seat_type == 'RW' ? '软卧' : '硬卧'))))}}</span></span>
                        <span>座位：<span style="font-weight: 500; display: unset;">{{ticket.seat}}</span></span>
                        <span style="font-size: 18px; margin-top: 5px;">价格：<span style="font-weight: 500; display: unset;">¥{{ticket.price}}</span></span>
                    </div>
                </el-card>
                <div class="operation-btns">
                    <el-button type="danger" @click="cancelOrder" :loading="isOrderCanceling" :disabled="isOrderPaying">取消订单</el-button>
                    <el-button type="success" @click="payOrder" :loading="isOrderPaying" :disabled="isOrderCanceling">支付订单</el-button>
                </div>
            </div>
            <div v-else-if="activeStep == 2" class="step-2">
                <el-card class="info-card" shadow="never">
                    <div slot="header" class="clearfix">
                        <span>列车信息</span>
                    </div>
                    <div class="content">
                        <div class="basic-info">
                            <div class="first">
                                <span style="font-size: 20px; font-weight: 500;">{{date}}</span>
                                <span style="font-size: 20px; font-weight: 500; margin-right: 5px;">（{{parseDay($moment(date).day())}}）</span>
                                <span style="font-size: 20px; font-weight: 500;">{{train.numbers ? JSON.parse(train.numbers)[0] : ''}}</span>
                                <span>次</span>
                            </div>
                            <div class="second">
                                <span style="font-size: 20px; font-weight: 500;">{{departStation.name}}</span>
                                <span>站</span>
                                <span style="font-size: 20px; font-weight: 500;">（{{departStation.pivot?$moment(departStation.pivot.depart_time, "HH:mm:ss").format('HH:mm'):''}}开）</span>
                                <span style="font-size: 20px; font-weight: 500;"><i class="far fa-long-arrow-alt-right"></i></span>
                                <span style="font-size: 20px; font-weight: 500;">{{arriveStation.name}}</span>
                                <span>站</span>
                                <span>（{{arriveStation.pivot?$moment(arriveStation.pivot.arrive_time, "HH:mm:ss").format('HH:mm'):''}}到）</span>
                            </div>
                        </div>
                    </div>
                </el-card>
                <el-card class="passenger-card" shadow="never">
                    <div slot="header" class="clearfix">
                        <span style="display: unset;">车票信息</span>
                        <el-tag style="float: right; margin-top: -3.5px; font-size: 13px;" type="success" size="medium">已支付</el-tag>
                    </div>
                    <div class="content">
                        <span>乘客：<span style="font-weight: 500; display: unset;">{{passenger.name}}</span></span>
                        <span>车厢：<span style="font-weight: 500; display: unset;">{{ticket.carriage}}</span></span>
                        <span>席位：<span style="font-weight: 500; display: unset;">{{ticket.seat_type == 'SZ' ? '商务座' : (ticket.seat_type == '1Z' ? '一等座' : (ticket.seat_type == '2Z' ? '二等座' : (ticket.seat_type == 'YZ' ? '硬座' : (ticket.seat_type == 'RW' ? '软卧' : '硬卧'))))}}</span></span>
                        <span>座位：<span style="font-weight: 500; display: unset;">{{ticket.seat}}</span></span>
                        <span style="font-size: 18px; margin-top: 5px;">价格：<span style="font-weight: 500; display: unset;">¥{{ticket.price}}</span></span>
                    </div>
                </el-card>
                <div class="operation-btns">
                    <el-button @click="router.push('/')">返回首页</el-button>
                    <el-button type="primary" @click="router.push('/order')">管理订单</el-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Book',
    data() {
        return {
            activeStep: 0,
            train_id: this.$route.query.ti,
            depart_station_id: this.$route.query.ds,
            arrive_station_id: this.$route.query.as,
            date: this.$route.query.dt,
            isTrainLoading: true,
            train: {},
            departStation: {},
            arriveStation: {},
            passengers: {},
            passengerForm: {
                passengerId: '',
                seatType: ''
            },
            passengerRules: {
                passengerId: [
                    { required: true, message: '请选择乘客', trigger: 'blur' },
                ],
                seatType: [
                    { required: true, message: '请选择席位', trigger: 'blur' },
                ]
            },
            isOrderSubmitting: false,
            order: {},
            ticket: {},
            passenger: {},
            isOrderPaying: false,
            isOrderCanceling: false,
        }
    },
    beforeRouteEnter (to, from, next) {
        if (to.query.ti == null || to.query.dt == null || to.query.ds == null || to.query.as == null) {
            next(vm => {
                vm.$message({
                    showClose: true,
                    message: '请先查询列车后再预订车票',
                    type: 'error'
                })
                vm.router.push('/')
            })
        } else {
            next(vm => {
                vm.$axios.get('/api/trains/' + to.query.ti + '?date=' + to.query.dt + '&depart_station_id=' + to.query.ds + '&arrive_station_id=' + to.query.as)
                .then(function (response) {
                    vm.train = response.data
                    for (let index = 0; index < vm.train.stations.length; index++) {
                        if (vm.train.stations[index].id == to.query.ds) {
                            vm.departStation = vm.train.stations[index]
                            break
                        }
                    }
                    for (let index = 0; index < vm.train.stations.length; index++) {
                        if (vm.train.stations[index].id == to.query.as) {
                            vm.arriveStation = vm.train.stations[index]
                            break
                        }
                    }
                    vm.$axios.get('/api/passengers')
                    .then(function (response2) {
                        for (let index = 0; index < response2.data.length; index++) {
                            switch (response2.data[index].type) {
                                case 'adult':
                                    response2.data[index].type_name = ['成人', 'success']
                                    break;
                                case 'child':
                                    response2.data[index].type_name = ['儿童', 'warning']
                                    break;
                                case 'student':
                                    response2.data[index].type_name = ['学生', 'primary']
                                    break;
                                case 'military':
                                    response2.data[index].type_name = ['残军伤警', 'danger']
                                    break;
                            }
                        }
                        vm.passengers = response2.data
                        vm.isTrainLoading = false
                    })
                })
            })
        }
        
    },
    created() {
        this.$emit('getTitle', '车票预订');
    },
    methods: {
        parseDay(day) {
            switch (day) {
                case 1:
                    return '周一'
                case 2:
                    return '周二'
                case 3:
                    return '周三'
                case 4:
                    return '周四'
                case 5:
                    return '周五'
                case 6:
                    return '周六'
                case 7:
                    return '周日'
            }
        },
        submitOrder() {
            this.$refs['passengerForm'].validate((err) => {
                if (err) {
                    this.isOrderSubmitting = true
                    this.$axios.post('/api/orders', {
                        train_id: this.train.id,
                        date: this.date,
                        depart_station: this.departStation.id,
                        arrive_station: this.arriveStation.id,
                        seat_type: this.passengerForm.seatType,
                        passenger_id: this.passengerForm.passengerId,
                    }).then((res) => {
                        const result = res.data
                        if (res.status == 200) {
                            this.order = result.order
                            this.ticket = result.ticket
                            this.train = result.train
                            this.passenger = result.passenger
                            this.isOrderSubmitting = false
                            this.activeStep = 1
                        } else {
                            this.isOrderSubmitting = false
                            this.$message({
                                showClose: true,
                                message: '订单创建失败，请稍后重试',
                                type: 'error'
                            });
                        }
                    }).catch((error) => {
                        this.$message({
                            showClose: true,
                            message: '订单创建失败：' + error.response.data.message,
                            type: 'error'
                        });
                        this.isOrderSubmitting = false
                    })
                }
            })
        },
        payOrder() {
            this.isOrderPaying = true
            this.$axios.post('/api/orders/' + this.order.id + '/pay').then((res) => {
                const result = res.data
                if (res.status == 200) {
                    this.order = result.order
                    this.isOrderPaying = false
                    this.activeStep = 2
                } else {
                    this.isOrderPaying = false
                    this.$message({
                        showClose: true,
                        message: '订单支付失败，请稍后重试',
                        type: 'error'
                    });
                }
            }).catch((error) => {
                this.$message({
                    showClose: true,
                    message: '订单支付失败：' + error.response.data.message,
                    type: 'error'
                });
                this.isOrderPaying = false
            })
        },
        cancelOrder() {
            this.isOrderCanceling = true
            this.$axios.delete('/api/orders/' + this.order.id).then((res) => {
                if (res.status == 200) {
                    this.isOrderCanceling = false
                    this.router.push('/')
                } else {
                    this.isOrderCanceling = false
                    this.$message({
                        showClose: true,
                        message: '订单取消失败，请稍后重试',
                        type: 'error'
                    });
                }
            }).catch((error) => {
                this.$message({
                    showClose: true,
                    message: '订单取消失败：' + error.response.data.message,
                    type: 'error'
                });
                this.isOrderCanceling = false
            })
        }
    },
}
</script>

<style lang="less" scoped>
.info-card,
.passenger-card {
    margin: 20px 0;
}
.info-card {
    .content {
        line-height: 24px;
        .basic-info {
            margin-bottom: 6px;
        }
        .seat-info {
            font-size: 14px;
        }
    }
}
.passenger-card {
    .content {
        .passenger-form {
            text-align: center;
            .el-form-item {
                margin-bottom: 0;
            }
        }
    }
}
.operation-btns {
    margin-top: 30px;
    text-align: center;
    button {
        padding: 13px 22px;
        font-size: 15px;
    }
}
.step-1,
.step-2 {
    .content {
        text-align: center;
    }
    .info-card {
        .first {
            margin-bottom: 5px;
        }
    }
    .passenger-card {
        span {
            display: block;
        }
    }
}
</style>
<style lang="less">
.book-process {
    .el-step__head.is-process {
        color: #409eff;
        border-color: #409eff;
    }
    .el-step__title.is-process {
        color: #409eff;
    }
}
.info-card,
.passenger-card {
    .el-card__header {
        padding: 10px 20px;
        width: unset;
        position: unset;
    }
    .el-card__body {
        height: unset;
        margin-top: unset;
    }
}
</style>