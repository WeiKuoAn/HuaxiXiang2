

<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <form class="d-flex flex-wrap align-items-center" action="{{ route('customer') }}" method="GET">
                                <div class="me-3">
                                    <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2" name="name" placeholder="姓名">
                                </div>
                                <div class="me-3">
                                    <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2" name="mobile" placeholder="電話">
                                </div>
                                <div class="me-3">
                                    <input type="search" class="form-control my-1 my-lg-0" id="inputPassword2" name="pet_name" placeholder="寶貝名">
                                </div>
                                {{-- <label for="status-select" class="me-2">Sort By</label> --}}
                                
                                <div class="me-3">
                                    <button type="submit" class="btn btn-success waves-effect waves-light me-1"><i class="fe-search me-1"></i>搜尋</button>
                                </div>
                            </form>
                        </div>
                    </div> <!-- end row -->
                </div>
            </div> <!-- end card -->
        </div> <!-- end col-->
    </div>
                    

</div> <!-- container -->

<script src="https://static.line-scdn.net/liff/edge/versions/2.3.0/sdk.js"></script>
<script>
  liff.init({ liffId: "2003225430-23p5QPad" }).then(() => {
    // LIFF 初始化完成
    if (!liff.isLoggedIn()) {
      liff.login(); // 自動登入
    }
    // 你可以在這裡撰寫更多與 LIFF 相關的邏輯
  });
</script>
