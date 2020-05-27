<?php

namespace App\Console\Commands;

use App\Models\Station;
use App\Models\Train;
use Illuminate\Console\Command;
use App\Models\User;

class ImportTrain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:importtrain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Trains';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $stations = ["许昌", "阳江", "炎陵", "惠州南", "嘉兴", "海口", "三门峡", "鹤壁东", "茂名", "潢川", "会同", "磁窑", "洛阳龙门", "岳阳", "东方", "渭南北", "瑞安", "渭南", "福田", "衡水", "鹰潭北", "百色", "汉川", "宁波", "邹城", "霸州", "义马", "济南", "樟树", "涪陵", "华山北", "宝鸡", "娄底南", "邢台东", "镇江南", "庐山", "平湖", "广宁", "哈密", "低庄", "福鼎", "泰山", "余杭", "来宾", "新余", "苏州园区", "定南", "连江", "酉阳", "潼关", "镇江", "北京", "株洲", "宜城", "盘州", "惠东", "聊城", "渑池南", "惠州", "渑池", "梅州西", "蚌埠南", "杭州东", "晋江", "乐昌东", "侯马", "商丘南", "诏安", "耒阳", "高安", "郑州西", "无锡东", "兰州", "宿州东", "余姚北", "天水", "南宁东", "赣州", "深圳坪山", "乐清", "贵阳北", "梧州南", "柳园", "涵江", "吴桥", "南昌", "金城江", "阳澄湖", "临澧", "角美", "鲘门", "坪石", "永州", "揭阳机场", "广州南", "温州南", "无锡", "韶关东", "韶山南", "汨罗", "赤壁", "漳州", "清河城", "贺州", "廉江", "商丘", "虎门", "正定机场", "安仁", "佛山西", "建桥", "佛山", "衡山西", "册亨", "成都", "宝丰", "巩义", "枣庄西", "漳浦", "张家口", "衢州", "德州东", "苍南", "咸丰", "醴陵东", "襄阳", "安化", "台州", "上海虹桥", "大同", "隆回", "邯郸东", "杨村", "嘉善南", "永城北", "鹰潭", "天津西", "枣庄", "全州南", "包头东", "天门南", "祁东", "宁乡", "永嘉", "恩平", "霍州", "滁州北", "呼和浩特东", "衡阳", "广水", "枝江北", "化州", "新化", "通道", "永济", "阳谷", "丹阳", "郑州东", "杭州", "广州东", "榆次", "湛江西", "仙游", "丰城", "定远", "新乡东", "泰安", "彭水", "新化南", "徐州", "平遥", "金华", "驻马店", "慈利", "昆明", "萍乡", "遂溪", "上海南", "长沙", "桂林北", "武昌", "红果", "昆山", "荆门", "兰考南", "靖州", "厦门北", "松江", "邓州", "吉安", "涿州东", "郁南", "咸宁北", "株洲西", "福清", "襄州", "光明城", "北京西", "灵宝西", "平顶山", "潮汕", "滕州", "韶关", "汉口", "阳朔", "邵东", "揭阳", "常平", "三水南", "绍兴东", "东光", "南江口", "南宁", "南京南", "三亚", "鲁山", "确山", "蚌埠", "福州", "苏州北", "饶平", "合川", "太谷", "深圳西", "龙南", "洛阳", "溆浦", "南昌西", "福州南", "龙川", "湘潭", "桃源", "三门县", "宜州", "武威", "乐昌", "益阳", "广州北", "云浮东", "源潭", "麻阳", "霞浦", "肇庆", "茶陵南", "绍兴北", "涟源", "宜春", "上饶", "重庆北", "定州东", "双峰北", "北海", "普宁", "铜仁", "张掖", "中山", "德州", "三门峡南", "萍乡北", "新会", "风陵渡", "安阳东", "东莞", "英德西", "金山北", "武汉", "娄底", "天津南", "驻马店西", "兖州", "潜江", "嘉兴南", "阜阳", "进贤", "庆盛", "大余", "济南西", "郴州", "新安县", "莆田", "雁荡山", "遂宁", "焦作", "包头", "醴陵", "兴义", "徐州东", "亳州", "深圳北", "常州北", "罗源", "宿州", "吐鲁番北", "乌鲁木齐", "威舍", "攸县南", "德安", "孝感北", "贵港", "永修", "东莞东", "呼和浩特", "怀化南", "荆州", "柳州", "奉化", "石家庄", "洞口", "九江", "秀山", "临河", "临汾", "深圳", "马踏", "绅坊", "永福南", "湛江", "清远", "廊坊北", "青县", "信阳东", "许昌东", "长沙南", "丰顺东", "松江南", "菏泽", "耒阳西", "西安", "雷州", "岳阳东", "昆明南", "运城", "玉林", "上海", "祁阳", "葵潭", "张家界", "文地", "嘉善", "海宁西", "畲江北", "潮阳", "丹阳北", "福安", "华山", "曲阜东", "云霄", "黔江", "苏州", "昆山南", "丹霞山", "湘潭北", "桐乡", "东安东", "辰溪", "北京南", "龙山北", "高碑店东", "富源", "保定东", "麻城", "衡山", "漯河", "信丰", "阳西", "郴州西", "郑州", "任丘", "桑植", "都匀东", "茂名西", "汝州", "汕尾", "深圳东", "河源", "衡阳东", "来凤", "田林", "常德", "肃宁", "张家界西", "高邑西", "吉首", "沧州西", "陆丰", "平阳", "钟山西", "台山", "诸暨", "曲靖", "滕州东", "赤壁北", "石门县北", "恭城", "太原", "邵阳", "徐闻", "温岭", "宁德", "南雄", "冷水江东", "泰和", "兴安北", "樟木头", "广州", "西安北", "当阳", "邵阳北", "滁州", "肇庆东", "河唇", "杨桥", "融安", "珠海", "南阳", "太姥山", "海宁", "汨罗东", "泉州", "汕头", "新余北", "沧州", "集宁南", "阳春", "常州", "开平南", "义乌", "漯河西", "西渡", "邵阳西", "南京", "信阳", "静海", "孝感", "宁海", "湘乡", "泊头", "咸宁", "怀集", "怀化", "桂林西", "宜昌东", "介休", "廊坊", "明港东", "嘉峪关"];

        // $pinyin = app('pinyin');

        $trains = json_decode(file_get_contents('./app/Console/Commands/all_train.json'),true);

        for ($i=0; $i < count($trains); $i++) {
            // 获得停站
            $stations = array();
            for ($j=0; $j < count($trains[$i]['line']); $j++) { 
                $station_name = $trains[$i]['line'][$j]['SNAME'];
                $station = Station::where('name', $station_name)->first();
                $arrive_time = $trains[$i]['line'][$j]['ATIME'];
                $depart_time = $trains[$i]['line'][$j]['STIME'];
                $train_num = $trains[$i]['line'][$j]['STCODE'];
                $station_num = $j + 1;
                $stations[$j] = [
                    'station_num' => $station_num,
                    'train_num' => $train_num,
                    'id' => $station->id,
                    'name' => $station->name,
                    'arrive_time' => $arrive_time ? $arrive_time : null,
                    'depart_time' => $depart_time ? $depart_time : null,
                ];
            }

            // 获得价格
            $price = array();
            $keys = array_keys($trains[$i]['price']);
            for ($j=0; $j < count($trains[$i]['price']); $j++) {
                if ($j == count($trains[$i]['price']) - 1) {
                    continue;
                }
                $price[$j + 1] = [];
                $station_name = $keys[$j];
                // $station = Station::where('name', $station_name)->first();
                $keys2 = array_keys($trains[$i]['price'][$station_name]);
                for ($k=0; $k < count($trains[$i]['price'][$station_name]); $k++) { 
                    $price[$j + 1][$j + $k + 2] = [];
                    $station_name2 = $keys2[$k];
                    // $station2 = Station::where('name', $station_name2)->first();
                    $keys3 = array_keys($trains[$i]['price'][$station_name][$station_name2]);
                    for ($m=0; $m < count($trains[$i]['price'][$station_name][$station_name2]); $m++) { 
                        $seat_type_name = $keys3[$m];
                        $seat_type = '';
                        switch ($seat_type_name) {
                            case '商务座':
                                $seat_type = 'SZ';
                                break;
                            case '特等座':
                                $seat_type = 'SZ';
                                break;
                            case '一等座':
                                $seat_type = '1Z';
                                break;
                            case '二等座':
                                $seat_type = '2Z';
                                break;
                            case '硬座':
                                $seat_type = 'YZ';
                                break;
                            case '软卧':
                                $seat_type = 'RW';
                                break;
                            case '硬卧':
                                $seat_type = 'YW';
                                break;
                            default:
                                break;
                        }
                        $price[$j + 1][$j + $k + 2][$seat_type] = $trains[$i]['price'][$station_name][$station_name2][$seat_type_name];
                    }
                }
            }


            $train = Train::create([
                'numbers' => json_encode($trains[$i]['train_code_2'] ? [$trains[$i]['train_code_1'], $trains[$i]['train_code_2']] : [$trains[$i]['train_code_1']]),
                'price' => $price
            ]);

            $pivots = array();
            for ($j=0; $j < count($stations); $j++) { 
                $pivots[$stations[$j]['id']] = [
                    'station_num' => $stations[$j]['station_num'],
                    'train_num'   => $stations[$j]['train_num'],
                    'arrive_time' => $stations[$j]['arrive_time'],
                    'depart_time' => $stations[$j]['depart_time']
                ];
            }
            // $stations_pivots = array_combine($stations, $pivots);

            $train->stations()->attach($pivots);
        }
    }
}
