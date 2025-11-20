@extends('layouts.vertical', ['page_title' => '延伸線上塔位 - AI 增強版'])

@section('css')
    <style>
        #3d-container {
            width: 100%;
            height: 700px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: relative;
            border-radius: 8px;
            overflow: hidden;
        }
        
        #3d-container:active {
            cursor: grabbing;
        }

        .control-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            min-width: 300px;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }

        .info-panel {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            min-width: 280px;
            max-width: 350px;
            display: none;
            backdrop-filter: blur(10px);
        }

        .info-panel.show {
            display: block;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .search-box {
            margin-bottom: 15px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }

        .ai-suggestions {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 12px;
            color: #666;
            max-height: 150px;
            overflow-y: auto;
        }

        .ai-suggestion-item {
            padding: 5px;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .ai-suggestion-item:hover {
            background: #e9ecef;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #4a90e2;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .niche-info {
            margin-top: 15px;
        }

        .niche-info h5 {
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
        }

        .niche-info p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }

        .highlighted {
            background: rgba(74, 144, 226, 0.2) !important;
            border: 2px solid #4a90e2 !important;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .btn-ai {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 10px;
            transition: transform 0.2s;
        }

        .btn-ai:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #999;
            padding: 5px 10px;
        }

        .close-btn:hover {
            color: #333;
        }

        .filter-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .filter-btn {
            padding: 6px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }

        .filter-btn.active {
            background: #4a90e2;
            color: white;
            border-color: #4a90e2;
        }

        .filter-btn:hover {
            background: #f0f0f0;
        }

        .filter-btn.active:hover {
            background: #357abd;
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
                            <li class="breadcrumb-item active">延伸線上塔位</li>
                        </ol>
                    </div>
                    <h4 class="page-title">延伸線上塔位 <span class="badge bg-primary">AI 增強版</span></h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body" style="position: relative; padding: 0;">
                        <!-- 控制面板 -->
                        <div class="control-panel">
                            <h5 style="margin-bottom: 15px; color: #333;">🔍 智能搜索與控制</h5>
                            
                            <div class="search-box">
                                <input type="text" id="search-input" placeholder="輸入塔位名稱或寵物名稱..." autocomplete="off">
                                <div class="ai-suggestions" id="ai-suggestions" style="display: none;"></div>
                            </div>

                            <div class="filter-buttons">
                                <button class="filter-btn active" data-filter="all">全部</button>
                                <button class="filter-btn" data-filter="with-photo">有照片</button>
                                <button class="filter-btn" data-filter="with-pet">有寵物</button>
                                <button class="filter-btn" data-filter="recent">最近新增</button>
                            </div>

                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-value" id="total-count">0</div>
                                    <div class="stat-label">總塔位數</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="visible-count">0</div>
                                    <div class="stat-label">顯示中</div>
                                </div>
                            </div>

                            <button class="btn-ai" onclick="generateAIDescription()">
                                🤖 AI 生成描述
                            </button>
                        </div>

                        <!-- 資訊面板 -->
                        <div class="info-panel" id="info-panel">
                            <button class="close-btn" onclick="closeInfoPanel()">&times;</button>
                            <div class="niche-info" id="niche-info-content">
                                <h5 id="niche-name">塔位資訊</h5>
                                <p><strong>寵物名稱：</strong><span id="niche-pet-name">-</span></p>
                                <p><strong>位置：</strong><span id="niche-position">-</span></p>
                                <p><strong>建立日期：</strong><span id="niche-date">-</span></p>
                                <p id="niche-ai-description" style="margin-top: 15px; padding: 10px; background: #f0f7ff; border-radius: 6px; font-style: italic; color: #4a90e2; display: none;"></p>
                            </div>
                        </div>

                        <!-- 3D 容器 -->
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
    {{-- 引入 OrbitControls - 使用可靠的 CDN --}}
    <script src="https://threejs.org/examples/js/controls/OrbitControls.js"></script>
    <script>
        // 如果上面的 CDN 失敗，使用備用實現
        if (typeof THREE !== 'undefined' && typeof THREE.OrbitControls === 'undefined') {
            console.log('使用備用 OrbitControls 實現');
            THREE.OrbitControls = function(camera, domElement) {
                this.camera = camera;
                this.domElement = domElement || document;
                this.enableDamping = true;
                this.dampingFactor = 0.05;
                this.minDistance = 5;
                this.maxDistance = 60;
                this.target = new THREE.Vector3(0, 0, 0);
                this.mouseButtons = { LEFT: 0, RIGHT: 2 };
                
                let isRotating = false, isPanning = false;
                let rotateStart = new THREE.Vector2();
                let panStart = new THREE.Vector2();
                
                const onMouseDown = (e) => {
                    if (e.button === 2) { // 右鍵 - 旋轉
                        isRotating = true;
                        rotateStart.set(e.clientX, e.clientY);
                    } else if (e.button === 0) { // 左鍵 - 平移
                        isPanning = true;
                        panStart.set(e.clientX, e.clientY);
                    }
                };
                
                const onMouseMove = (e) => {
                    if (isRotating) {
                        const delta = new THREE.Vector2(e.clientX - rotateStart.x, e.clientY - rotateStart.y);
                        const spherical = new THREE.Spherical();
                        spherical.setFromVector3(camera.position.clone().sub(this.target));
                        spherical.theta -= delta.x * 0.01;
                        spherical.phi += delta.y * 0.01;
                        spherical.phi = Math.max(0.1, Math.min(Math.PI - 0.1, spherical.phi));
                        camera.position.setFromSpherical(spherical).add(this.target);
                        camera.lookAt(this.target);
                        rotateStart.set(e.clientX, e.clientY);
                    } else if (isPanning) {
                        const delta = new THREE.Vector2(e.clientX - panStart.x, e.clientY - panStart.y);
                        const pan = new THREE.Vector3(-delta.x * 0.01, delta.y * 0.01, 0);
                        pan.applyQuaternion(camera.quaternion);
                        camera.position.add(pan);
                        this.target.add(pan);
                        panStart.set(e.clientX, e.clientY);
                    }
                };
                
                const onMouseUp = () => { isRotating = false; isPanning = false; };
                const onWheel = (e) => {
                    e.preventDefault();
                    const scale = e.deltaY > 0 ? 1.1 : 0.9;
                    const direction = camera.position.clone().sub(this.target).multiplyScalar(scale);
                    camera.position.copy(this.target.clone().add(direction));
                };
                
                domElement.addEventListener('mousedown', onMouseDown);
                domElement.addEventListener('mousemove', onMouseMove);
                domElement.addEventListener('mouseup', onMouseUp);
                domElement.addEventListener('wheel', onWheel);
                domElement.addEventListener('contextmenu', (e) => e.preventDefault());
                
                this.update = () => { camera.lookAt(this.target); };
            };
        }
    </script>

    <script>
        // 全局變數
        let scene, camera, renderer, controls, raycaster, mouse;
        let nicheGroups = [];
        let allNicheData = [];
        let filteredNicheData = [];
        let selectedNiche = null;
        let currentFilter = 'all';

        // AI 智能搜索建議（模擬 Gemini3 功能）
        const aiSuggestions = {
            generate: function(query) {
                if (!query || query.length < 2) return [];
                
                // 智能關鍵字提取和建議
                const suggestions = [];
                const lowerQuery = query.toLowerCase();
                
                // 基於查詢生成智能建議
                allNicheData.forEach(niche => {
                    const name = (niche.name || '').toLowerCase();
                    const petName = (niche.pet_name || '').toLowerCase();
                    
                    if (name.includes(lowerQuery) || petName.includes(lowerQuery)) {
                        suggestions.push({
                            text: niche.pet_name ? `${niche.name} - ${niche.pet_name}` : niche.name,
                            niche: niche
                        });
                    }
                });

                // AI 語義搜索（模擬）
                if (suggestions.length === 0) {
                    // 模糊匹配
                    allNicheData.forEach(niche => {
                        const name = (niche.name || '').toLowerCase();
                        const petName = (niche.pet_name || '').toLowerCase();
                        const queryWords = lowerQuery.split(' ');
                        
                        queryWords.forEach(word => {
                            if (word.length > 1 && (name.includes(word) || petName.includes(word))) {
                                if (!suggestions.find(s => s.niche.id === niche.id)) {
                                    suggestions.push({
                                        text: niche.pet_name ? `${niche.name} - ${niche.pet_name}` : niche.name,
                                        niche: niche
                                    });
                                }
                            }
                        });
                    });
                }

                return suggestions.slice(0, 5);
            },
            
            generateDescription: function(niche) {
                // 模擬 AI 生成描述
                const descriptions = [
                    `這是一個${niche.pet_name ? '為 ' + niche.pet_name + ' 準備的' : ''}莊嚴塔位，位於第 ${Math.floor(niche.position_y)} 層，${niche.photo_urls && niche.photo_urls.length > 0 ? '配有紀念照片' : '簡約設計'}。`,
                    `塔位 ${niche.name}${niche.pet_name ? ' - ' + niche.pet_name + ' 的安息之所' : ''}，${niche.created_at ? '建立於 ' + niche.created_at : '永久紀念'}。`,
                    `這是一個精心設計的塔位空間${niche.pet_name ? '，紀念 ' + niche.pet_name : ''}，${niche.photo_urls && niche.photo_urls.length > 0 ? '保存著珍貴的回憶照片' : '簡潔而莊重'}。`
                ];
                return descriptions[Math.floor(Math.random() * descriptions.length)];
            }
        };

        function init3DScene(container) {
            console.log('開始初始化 3D 場景...');
            console.log('容器尺寸:', container.clientWidth, 'x', container.clientHeight);
            
            if (typeof THREE === 'undefined') {
                console.error('Three.js 函式庫載入失敗。');
                container.innerHTML = '<div class="alert alert-danger" style="padding: 20px; color: red;">錯誤：無法載入 3D 函式庫。請檢查網路連線或重新載入頁面。</div>';
                return;
            }
            
            console.log('Three.js 版本:', THREE.REVISION);
            
            if (!window.WebGLRenderingContext) {
                container.innerHTML = '<div class="alert alert-warning" style="padding: 20px; color: orange;">您的瀏覽器不支援 WebGL，無法顯示 3D 內容。</div>';
                return;
            }
            
            // 確保容器有高度
            if (container.clientHeight === 0) {
                container.style.height = '700px';
                console.log('強制設定容器高度為 700px');
            }

            // 初始化場景
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0x0a0a1a);

            camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
            // 調整攝影機位置，讓塔位牆更清楚可見
            camera.position.set(8, 6, 12);
            camera.lookAt(0, 0, 0);

            renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
            renderer.setSize(container.clientWidth, container.clientHeight);
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            container.appendChild(renderer.domElement);

            // 控制器
            try {
                if (typeof THREE.OrbitControls !== 'undefined') {
                    controls = new THREE.OrbitControls(camera, renderer.domElement);
                } else {
                    // 如果 OrbitControls 未載入，嘗試使用內建方式
                    console.warn('OrbitControls 未找到，嘗試替代方案');
                    // 創建簡單的控制器
                    controls = {
                        enableDamping: true,
                        dampingFactor: 0.05,
                        update: function() {},
                        target: new THREE.Vector3(0, 0, 0)
                    };
                }
                
                if (controls && controls.enableDamping !== undefined) {
                    controls.enableDamping = true;
                    controls.dampingFactor = 0.05;
                    controls.minDistance = 5;
                    controls.maxDistance = 60;
                    if (controls.target) {
                        controls.target.set(0, 0, 0);
                    }
                    if (controls.mouseButtons) {
                        controls.mouseButtons.LEFT = THREE.MOUSE.PAN;
                        controls.mouseButtons.RIGHT = THREE.MOUSE.ROTATE;
                    }
                }
            } catch (e) {
                console.error('控制器初始化失敗:', e);
                controls = { update: function() {}, target: new THREE.Vector3(0, 0, 0) };
            }

            // 光源系統（增強版）
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
            scene.add(ambientLight);

            const directionalLight1 = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight1.position.set(10, 10, 5);
            directionalLight1.castShadow = true;
            scene.add(directionalLight1);

            const directionalLight2 = new THREE.DirectionalLight(0xffffff, 0.3);
            directionalLight2.position.set(-10, 5, -5);
            scene.add(directionalLight2);

            const pointLight = new THREE.PointLight(0x4a90e2, 0.5, 100);
            pointLight.position.set(0, 10, 0);
            scene.add(pointLight);

            // 格線（更大更清楚）
            const gridHelper = new THREE.GridHelper(40, 40, 0x555555, 0x333333);
            scene.add(gridHelper);
            
            // 添加座標軸輔助器（可選，幫助理解 3D 空間）
            // const axesHelper = new THREE.AxesHelper(5);
            // scene.add(axesHelper);

            // Raycaster 用於點擊檢測（Three.js 內建，不需要單獨載入）
            raycaster = new THREE.Raycaster();
            mouse = new THREE.Vector2();
            
            console.log('3D 場景基本設置完成');

            // 點擊事件
            renderer.domElement.addEventListener('click', onNicheClick, false);
            renderer.domElement.addEventListener('mousemove', onMouseMove, false);

            // 載入資料
            loadNicheData();

            // 動畫循環
            function animate() {
                requestAnimationFrame(animate);
                if (controls && controls.update) {
                    controls.update();
                }
                renderer.render(scene, camera);
            }
            animate();
            console.log('動畫循環已啟動');

            // 視窗大小調整
            function onWindowResize() {
                const width = container.clientWidth;
                const height = container.clientHeight;
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
                renderer.setSize(width, height);
            }
            window.addEventListener('resize', onWindowResize, false);
        }

        function createNiche(data, isHighlighted = false) {
            const nicheGroup = new THREE.Group();
            nicheGroup.userData = data;
            
            const nicheSize = 1.2;
            const nicheDepth = 0.8;
            const wallThickness = 0.05;

            // 增強材質
            const wallMaterial = new THREE.MeshStandardMaterial({
                color: isHighlighted ? 0x4a90e2 : 0x888888,
                roughness: 0.7,
                metalness: 0.3,
                emissive: isHighlighted ? 0x1a3a5a : 0x000000,
                emissiveIntensity: isHighlighted ? 0.2 : 0
            });

            // 建立塔位結構
            const backGeo = new THREE.BoxGeometry(nicheSize, nicheSize, wallThickness);
            const back = new THREE.Mesh(backGeo, wallMaterial);
            back.position.z = -nicheDepth / 2;
            back.castShadow = true;
            nicheGroup.add(back);

            const topGeo = new THREE.BoxGeometry(nicheSize, wallThickness, nicheDepth);
            const top = new THREE.Mesh(topGeo, wallMaterial);
            top.position.y = nicheSize / 2 - wallThickness / 2;
            top.castShadow = true;
            nicheGroup.add(top);

            const bottom = new THREE.Mesh(topGeo, wallMaterial);
            bottom.position.y = -nicheSize / 2 + wallThickness / 2;
            bottom.castShadow = true;
            nicheGroup.add(bottom);

            const sideGeo = new THREE.BoxGeometry(wallThickness, nicheSize - (wallThickness * 2), nicheDepth);
            const left = new THREE.Mesh(sideGeo, wallMaterial);
            left.position.x = -nicheSize / 2 + wallThickness / 2;
            left.castShadow = true;
            nicheGroup.add(left);

            const right = new THREE.Mesh(sideGeo, wallMaterial);
            right.position.x = nicheSize / 2 - wallThickness / 2;
            right.castShadow = true;
            nicheGroup.add(right);

            // 照片處理
            const textureLoader = new THREE.TextureLoader();
            if (data.photo_urls && data.photo_urls.length > 0) {
                const photoCount = data.photo_urls.length;
                const photoDisplayAreaWidth = nicheSize * 0.8;
                const photoSize = photoDisplayAreaWidth / photoCount;
                const gap = photoCount > 1 ? 0.05 : 0;

                data.photo_urls.forEach((url, index) => {
                    textureLoader.load(
                        url,
                        (texture) => {
                            const photoMaterial = new THREE.MeshBasicMaterial({ map: texture });
                            const photoGeometry = new THREE.PlaneGeometry(photoSize - gap, photoSize - gap);
                            const photo = new THREE.Mesh(photoGeometry, photoMaterial);
                            const xOffset = -((photoCount - 1) * photoSize) / 2 + index * photoSize;
                            photo.position.x = xOffset;
                            photo.position.z = -nicheDepth / 2 + (wallThickness / 2) + 0.01;
                            nicheGroup.add(photo);
                        },
                        undefined,
                        (error) => {
                            console.warn('圖片載入失敗:', url, error);
                        }
                    );
                });
            }

            nicheGroup.position.set(data.position_x, data.position_y, data.position_z);
            scene.add(nicheGroup);
            nicheGroups.push(nicheGroup);
            
            return nicheGroup;
        }

        function loadNicheData() {
            fetch('{{ route("columbarium.getData") }}')
                .then(response => response.json())
                .then(data => {
                    // 如果資料為空，使用假資料
                    if (!data || data.length === 0) {
                        console.log('資料庫為空，使用假資料展示');
                        generateMockData();
                    } else {
                        allNicheData = data;
                        filteredNicheData = data;
                        updateStats();
                        renderNiches();
                    }
                })
                .catch(error => {
                    console.error('載入資料失敗:', error);
                    // 使用假資料作為後備
                    generateMockData();
                });
        }

        function generateMockData() {
            const mockData = [];
            // 增加塔位數量，讓畫面更豐富
            const rows = 6;
            const cols = 8;
            const spacing = 1.5;
            const offsetX = -(cols / 2) * spacing + (spacing / 2);
            const offsetY = (rows / 2) * spacing - (spacing / 2);

            // 隨機選擇幾個塔位有照片和寵物名稱
            const specialNiches = [
                { row: 2, col: 3, pet: '小寶', hasPhoto: true },
                { row: 3, col: 4, pet: '小花', hasPhoto: true },
                { row: 1, col: 5, pet: '小黑', hasPhoto: false },
                { row: 4, col: 2, pet: '小白', hasPhoto: true },
            ];

            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    const special = specialNiches.find(s => s.row === r && s.col === c);
                    const hasPhoto = special && special.hasPhoto;
                    const petName = special ? special.pet : null;
                    
                    mockData.push({
                        id: r * cols + c + 1,
                        name: `塔位 ${String.fromCharCode(65 + r)}-${c + 1}`,
                        pet_name: petName,
                        photo_urls: hasPhoto ? ['https://img.shoplineapp.com/media/image_clips/62134cd7aea3ad002c617cf6/original.png?1645432022'] : null,
                        position_x: c * spacing + offsetX,
                        position_y: r * spacing + offsetY,
                        position_z: 0,
                        created_at: new Date(Date.now() - Math.random() * 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
                    });
                }
            }
            allNicheData = mockData;
            filteredNicheData = mockData;
            updateStats();
            renderNiches();
        }

        function renderNiches() {
            // 清除現有塔位
            nicheGroups.forEach(group => {
                scene.remove(group);
                group.traverse(child => {
                    if (child.geometry) child.geometry.dispose();
                    if (child.material) {
                        if (Array.isArray(child.material)) {
                            child.material.forEach(mat => mat.dispose());
                        } else {
                            child.material.dispose();
                        }
                    }
                });
            });
            nicheGroups = [];

            // 渲染過濾後的塔位
            filteredNicheData.forEach(niche => {
                const isHighlighted = selectedNiche && selectedNiche.id === niche.id;
                createNiche(niche, isHighlighted);
            });
        }

        function onNicheClick(event) {
            const rect = renderer.domElement.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

            raycaster.setFromCamera(mouse, camera);
            const intersects = raycaster.intersectObjects(nicheGroups, true);

            if (intersects.length > 0) {
                const clickedGroup = intersects[0].object.parent;
                if (clickedGroup && clickedGroup.userData) {
                    selectNiche(clickedGroup.userData);
                }
            } else {
                closeInfoPanel();
            }
        }

        function onMouseMove(event) {
            const rect = renderer.domElement.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

            raycaster.setFromCamera(mouse, camera);
            const intersects = raycaster.intersectObjects(nicheGroups, true);

            renderer.domElement.style.cursor = intersects.length > 0 ? 'pointer' : 'default';
        }

        function selectNiche(niche) {
            selectedNiche = niche;
            showInfoPanel(niche);
            renderNiches(); // 重新渲染以高亮顯示
        }

        function showInfoPanel(niche) {
            document.getElementById('niche-name').textContent = niche.name;
            document.getElementById('niche-pet-name').textContent = niche.pet_name || '-';
            document.getElementById('niche-position').textContent = `(${niche.position_x.toFixed(1)}, ${niche.position_y.toFixed(1)}, ${niche.position_z.toFixed(1)})`;
            document.getElementById('niche-date').textContent = niche.created_at || '-';
            
            const aiDesc = document.getElementById('niche-ai-description');
            aiDesc.style.display = 'none';
            
            document.getElementById('info-panel').classList.add('show');
        }

        function closeInfoPanel() {
            document.getElementById('info-panel').classList.remove('show');
            selectedNiche = null;
            renderNiches();
        }

        function updateStats() {
            document.getElementById('total-count').textContent = allNicheData.length;
            document.getElementById('visible-count').textContent = filteredNicheData.length;
        }

        // 搜索功能
        document.getElementById('search-input').addEventListener('input', function(e) {
            const query = e.target.value.trim();
            const suggestionsDiv = document.getElementById('ai-suggestions');
            
            if (query.length >= 2) {
                const suggestions = aiSuggestions.generate(query);
                if (suggestions.length > 0) {
                    suggestionsDiv.innerHTML = suggestions.map(s => 
                        `<div class="ai-suggestion-item" onclick="selectSuggestion('${s.niche.id}')">${s.text}</div>`
                    ).join('');
                    suggestionsDiv.style.display = 'block';
                } else {
                    suggestionsDiv.style.display = 'none';
                }
            } else {
                suggestionsDiv.style.display = 'none';
            }

            filterNiches(query);
        });

        function selectSuggestion(nicheId) {
            const niche = allNicheData.find(n => n.id == nicheId);
            if (niche) {
                selectNiche(niche);
                // 移動攝影機到該塔位
                camera.position.set(
                    niche.position_x + 3,
                    niche.position_y + 2,
                    niche.position_z + 5
                );
                controls.target.set(niche.position_x, niche.position_y, niche.position_z);
                document.getElementById('search-input').value = niche.pet_name ? `${niche.name} - ${niche.pet_name}` : niche.name;
                document.getElementById('ai-suggestions').style.display = 'none';
            }
        }

        function filterNiches(query = '') {
            let filtered = allNicheData;

            // 文字搜索
            if (query) {
                const lowerQuery = query.toLowerCase();
                filtered = filtered.filter(niche => {
                    const name = (niche.name || '').toLowerCase();
                    const petName = (niche.pet_name || '').toLowerCase();
                    return name.includes(lowerQuery) || petName.includes(lowerQuery);
                });
            }

            // 篩選器
            switch (currentFilter) {
                case 'with-photo':
                    filtered = filtered.filter(niche => niche.photo_urls && niche.photo_urls.length > 0);
                    break;
                case 'with-pet':
                    filtered = filtered.filter(niche => niche.pet_name);
                    break;
                case 'recent':
                    filtered = filtered.filter(niche => {
                        if (!niche.created_at) return false;
                        const date = new Date(niche.created_at);
                        const daysAgo = (Date.now() - date.getTime()) / (1000 * 60 * 60 * 24);
                        return daysAgo <= 30;
                    });
                    break;
            }

            filteredNicheData = filtered;
            updateStats();
            renderNiches();
        }

        // 篩選按鈕
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                filterNiches(document.getElementById('search-input').value.trim());
            });
        });

        function generateAIDescription() {
            if (!selectedNiche) {
                alert('請先選擇一個塔位');
                return;
            }
            
            const description = aiSuggestions.generateDescription(selectedNiche);
            const aiDesc = document.getElementById('niche-ai-description');
            aiDesc.textContent = description;
            aiDesc.style.display = 'block';
        }

        // 初始化
        $(function() {
            console.log('jQuery 已載入，準備初始化 3D 場景');
            
            // 等待 DOM 完全載入
            setTimeout(function() {
                const container = document.getElementById('3d-container');
                if (!container) {
                    console.error("錯誤：找不到 3D 容器。");
                    return;
                }
                
                console.log('找到 3D 容器，開始初始化');
                console.log('容器當前尺寸:', container.clientWidth, 'x', container.clientHeight);
                
                // 確保容器有高度
                if (container.clientHeight === 0 || container.clientHeight < 100) {
                    container.style.height = '700px';
                    console.log('容器高度為 0，已設定為 700px');
                }
                
                // 再次延遲確保尺寸已更新
                setTimeout(function() {
                    init3DScene(container);
                }, 50);
            }, 200);
        });
        
        // 如果 jQuery 未載入，使用原生 JavaScript
        if (typeof $ === 'undefined') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const container = document.getElementById('3d-container');
                    if (container) {
                        console.log('使用原生 JavaScript 初始化');
                        if (container.clientHeight === 0) {
                            container.style.height = '700px';
                        }
                        setTimeout(function() {
                            init3DScene(container);
                        }, 50);
                    }
                }, 200);
            });
        }
    </script>
@endsection
