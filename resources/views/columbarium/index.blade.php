@extends('layouts.vertical', ['page_title' => '線上塔位'])

@section('css')
    <style>
        #3d-container {
            width: 100%;
            /* 高度將由 JavaScript 動態設定 */
            background-color: #1a1a1a;
        }
        #3d-container:active {
            cursor: grabbing;
        }
    </style>
@endsection

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Huaxixiang</a></li>
                            <li class="breadcrumb-item active">線上塔位</li>
                        </ol>
                    </div>
                    <h4 class="page-title">線上塔位</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div id="3d-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container -->
@endsection

@section('script')
    {{-- 引入 Three.js 核心函式庫 --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    {{-- 引入 OrbitControls 以便用滑鼠控制視角 --}}
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

    <script>
        function init3DScene(container) {
            // 檢查 Three.js 是否成功載入
            if (typeof THREE === 'undefined') {
                console.error('Three.js 函式庫載入失敗。');
                container.innerHTML = '<div class="alert alert-danger">錯誤：無法載入 3D 函式庫。</div>';
                return;
            }
            // 檢查 WebGL 是否可用
            if (!window.WebGLRenderingContext) {
                container.innerHTML = '<div class="alert alert-warning">您的瀏覽器不支援 WebGL，無法顯示 3D 內容。</div>';
                return;
            }

            // 1. 初始化場景、攝影機和渲染器
            const scene = new THREE.Scene();
            scene.background = new THREE.Color(0x111111); // 為場景加上深灰色背景

            const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
            // 調整攝影機位置，以便看到整個塔位牆
            camera.position.set(0, 0, 10);
            const renderer = new THREE.WebGLRenderer({ antialias: true }); // 開啟抗鋸齒

            renderer.setSize(container.clientWidth, container.clientHeight);
            container.appendChild(renderer.domElement);

            // 2. 加入攝影機控制器 (OrbitControls)
            const controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true; // 啟用阻尼效果，讓旋轉更平滑
            controls.dampingFactor = 0.05;

            // --- 更改滑鼠按鍵設定 ---
            // 預設是左鍵旋轉、右鍵平移。我們將其對調，讓操作更直覺。
            controls.mouseButtons.LEFT = THREE.MOUSE.PAN; // 左鍵拖曳 -> 平移 (Pan)
            controls.mouseButtons.RIGHT = THREE.MOUSE.ROTATE; // 右鍵拖曳 -> 旋轉 (Orbit)

            // 3. 加入光源
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6); // 環境光
            scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 1.0); // 平行光
            directionalLight.position.set(5, 10, 7.5);
            scene.add(directionalLight);

            // 輔助工具：加入格線，方便觀察 3D 空間
            const gridHelper = new THREE.GridHelper(20, 20); // 格線放大
            scene.add(gridHelper);

            const textureLoader = new THREE.TextureLoader();

            // 建立一個塔位的函數
            function createNiche(data) {
                const nicheGroup = new THREE.Group();
                const nicheSize = 1.2;
                const nicheDepth = 0.8;
                const wallThickness = 0.05;

                const wallMaterial = new THREE.MeshStandardMaterial({
                    color: 0x888888, // 塔位牆壁顏色
                    roughness: 0.8,
                    metalness: 0.2
                });

                // 使用多個 BoxGeometry 組成一個空心盒子
                const backGeo = new THREE.BoxGeometry(nicheSize, nicheSize, wallThickness);
                const back = new THREE.Mesh(backGeo, wallMaterial);
                back.position.z = -nicheDepth / 2;
                nicheGroup.add(back);

                const topGeo = new THREE.BoxGeometry(nicheSize, wallThickness, nicheDepth);
                const top = new THREE.Mesh(topGeo, wallMaterial);
                top.position.y = nicheSize / 2 - wallThickness / 2;
                nicheGroup.add(top);

                const bottom = new THREE.Mesh(topGeo, wallMaterial);
                bottom.position.y = -nicheSize / 2 + wallThickness / 2;
                nicheGroup.add(bottom);

                const sideGeo = new THREE.BoxGeometry(wallThickness, nicheSize - (wallThickness * 2), nicheDepth);
                const left = new THREE.Mesh(sideGeo, wallMaterial);
                left.position.x = -nicheSize / 2 + wallThickness / 2;
                nicheGroup.add(left);

                const right = new THREE.Mesh(sideGeo, wallMaterial);
                right.position.x = nicheSize / 2 - wallThickness / 2;
                nicheGroup.add(right);

                // 如果有照片，則建立照片平面
                if (data.photo_urls && data.photo_urls.length > 0) {
                    const photoCount = data.photo_urls.length;
                    const photoDisplayAreaWidth = nicheSize * 0.8; // 使用 80% 的塔位寬度來顯示照片
                    const photoSize = photoDisplayAreaWidth / photoCount;
                    const gap = photoCount > 1 ? 0.05 : 0; // 照片間的間隙

                    data.photo_urls.forEach((url, index) => {
                        const texture = textureLoader.load(url);
                        const photoMaterial = new THREE.MeshBasicMaterial({ map: texture });
                        // 讓照片是正方形的
                        const photoGeometry = new THREE.PlaneGeometry(photoSize - gap, photoSize - gap);
                        const photo = new THREE.Mesh(photoGeometry, photoMaterial);

                        // 計算每張照片的位置，使其在塔位內水平置中排列
                        const xOffset = -((photoCount - 1) * photoSize) / 2 + index * photoSize;
                        photo.position.x = xOffset;
                        photo.position.z = -nicheDepth / 2 + (wallThickness / 2) + 0.01; // 貼在背板上
                        nicheGroup.add(photo);
                    });
                }

                nicheGroup.position.set(data.position_x, data.position_y, data.position_z);
                scene.add(nicheGroup);
            }

            // 6. 建立動畫循環
            function animate() {
                requestAnimationFrame(animate);

                // 更新控制器
                controls.update();

                renderer.render(scene, camera);
            }

            // 7. 處理視窗大小變更
            function onWindowResize() {
                const width = container.clientWidth;
                const height = container.clientHeight;

                camera.aspect = width / height;
                camera.updateProjectionMatrix();
                renderer.setSize(width, height);
            }

            window.addEventListener('resize', onWindowResize, false);

            // 啟動動畫
            animate();
            console.log("--- 3D 場景初始化成功 ---");

            // --- 使用假資料 (Mock Data) ---
            const mockData = [];
            const rows = 5;
            const cols = 7;
            const spacing = 1.5; // 塔位間距
            const offsetX = - (cols / 2) * spacing + (spacing / 2);
            const offsetY = (rows / 2) * spacing - (spacing / 2);

            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    // 讓中間的塔位有照片
                    const isCenter = (r === Math.floor(rows / 2) && c === Math.floor(cols / 2));
                    mockData.push({
                        name: `塔位 ${r + 1}-${c + 1}`,
                        // 確保您的 public/storage/pet_photos/dog.jpg 和 cat.jpg 存在
                        photo_urls: isCenter ? ['https://img.shoplineapp.com/media/image_clips/62134cd7aea3ad002c617cf6/original.png?1645432022'] : null,
                        position_x: c * spacing + offsetX,
                        position_y: r * spacing + offsetY,
                        position_z: 0
                    });
                }
            }

            // 根據假資料建立塔位
            mockData.forEach(nicheData => createNiche(nicheData));
        }

        $(function () {
            setTimeout(function() {
                const container = document.getElementById('3d-container');
                if (!container) {
                    console.error("錯誤：找不到 3D 容器。");
                    return;
                }

                // 由於 CSS 高度計算問題，我們用 JS 強制設定容器高度
                container.style.height = '600px';

                if (container.clientHeight === 0) {
                    console.error("錯誤：即使在 JS 中設定後，容器高度仍然為零。");
                    return;
                }

                // 呼叫初始化函數
                init3DScene(container);
            }, 100); // 稍微延遲，確保 DOM 元素尺寸已計算
        });
    </script>
@endsection