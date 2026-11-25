<?php

// Customer sending an inquiry
if (isset($_POST['Sendmsg'])) 
    {
      $name = $_POST['name'];
      $email = $_POST['email'];
      $tel = $_POST['mobile'];
      $select = $_POST['select'];
      $message = $_POST['msg'];

      require_once 'config.php';
      $query=mysqli_query($con,"INSERT INTO contactus(Name,Email,Mobile,Subject,Message)values('".$name."','".$email."','".$tel."','".$select."','".$message."')");
      
      //Getting the new ID that's auto incremented
      $id= mysqli_insert_id($con);

      if ($query)
      {
           
           $newinquiry=mysqli_query($con, "select * from contactus where Inquiry_ID='".$id."'");
           $inquiry=mysqli_fetch_assoc($newinquiry);
           

           //convert the data into a text for whatsapp message
           $convertto_text="Name : ".$inquiry['Name'];
           $convertto_text.="\nEmail : ".$inquiry['Email'];
           $convertto_text.="\nMobile : ".$inquiry['Mobile'];
           $convertto_text.="\nSubject : ".$inquiry['Subject'];
           $convertto_text.="\nMessage : ".$inquiry['Message'];
           
           $whatsapp_url = "https://api.whatsapp.com/send?phone=94777203659&text=".urlencode($convertto_text);
           header("Location: $whatsapp_url");
           exit();
      }

      else
      {
        die("Database Query Error: " . mysqli_error($con));  
      }
    
    }
?>