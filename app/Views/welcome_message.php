<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<div class="container" >
    <div class="row">
        <div class="col-md-12">
    <br><br>
<!-- Pills content -->
<div class="tab-content">
  <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="tab-login">
    <form method="POST" action="<?php echo base_url(); ?>daftar">
      <div class="text-center mb-3 h-100 align-middle">
      <!-- Email input -->

      <h2>Registration Details<br> : Montessor Seminar with Dr. Paul Epstein</h2>
      <br>
      <div data-mdb-input-init class="form-outline mb-4">
        <label class="form-label" for="loginName">FULL NAME (For Certificate):</label>
        <input type="text" id="loginName" name="nama" class="form-control" />
      </div>
      <div data-mdb-input-init class="form-outline mb-4">
        <label class="form-label" for="loginName">E-mail:</label>
        <input type="email" id="loginName" name="email" class="form-control" />
      </div>
     <!--  <div data-mdb-input-init class="form-outline mb-4">
        <label class="form-label" for="loginName">Tempat, Tanggal Lahir</label>
        <input type="text" id="loginName" name="ttl" class="form-control" />
      </div> -->
      <div data-mdb-input-init class="form-outline mb-4">
        <label class="form-label" for="loginName">Phone Number:</label>
        <input type="text" id="loginName" name="hp" class="form-control" />
      </div>
      <div data-mdb-input-init class="form-outline mb-4">
        <label class="form-label" for="loginName">Occupation:</label>
        <input type="text" id="loginName" name="occupation" class="form-control" />
      </div>

      <!-- Password input -->
      <div data-mdb-input-init class="form-outline mb-4">
        
      </div>

      <!-- 2 column grid layout -->

      <!-- Submit button -->
      <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">Register</button>

      <!-- Register buttons -->
      
    </div>
    </form>
  </div>
</div>
<!-- Pills content -->
        </div>
    </div>
</div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>



</html>
