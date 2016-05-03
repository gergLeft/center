<?php if ($loginProtectedPage) : ?>
<form method="POST">
	<div>
		<LoggedInTemplate>
	    <nav class="top-bar">
	      <ul class="title-area">
	        <!-- Title Area -->
	        <li class="name">
	          <h1><a href="/dashboard.php">Dashboard</a></h1>
	          <!--
	          <span class="hide-for-large-up">
	            <a href="profile.php"><%= currentUser %></a>
	            <asp:linkbutton id="btnLogout2" class="button" text="Logout" runat="server" onclick="btnLogout_Click" />
	          </span>
	          -->
	        </li>
	        <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
	        <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
	      </ul>
	    
	      <section class="top-bar-section">
	        <!-- Left Nav Section -->
	  		  <ul class="left">
	    	   	<?php /*
                <li class="divider"></li>
                <li class="active"><a href="schedule.php">Schedule</a></li>
	      
                <li class="divider"></li>
                <li><a href="budget.php">Budget</a></li>
	      
                <li class="divider"></li>
                <li class="has-dropdown"><a href="#">Lists</a>
                  <ul class="dropdown">
                    <li><a href="	chores.php">Chores</a>
                    <li><a href="meals.php">Meals</a></li>
                    <li><a href="groceries.php">Grocery List</a></li>
                    <li><a href="projects.php">Projects</a></li>
                    <li><a href="tasks.php">Tasks</a></li>
                    <li><a href="wishlists.php">Wishlists</a></li>
                  </ul>
                </li>
                
                <li class="divider"></li>
                <li class="has-dropdown"><a href="classwork_main.php">Classwork</a>
                  <ul class="dropdown">
                    <li><a href="classwork_current.php">Current Curriculum</a></li>
                    <li class="divider"></li>
                    <li><a href="classwork_classes.php">Class List</a></li>
                    <li><a href="classwork_students.php">Student List</a></li>
                  </ul>
                </li>
                
	          <li class="divider"></li>
	          <li><a href="family.php">Family</a></li>
              */?>
              
	        </ul>
            <?php //active if in a site section (budget, schedule, projects, etc...) ?>
            
            <?php 
              $sections[] = array (
                "title" => "Budget",
                "actions" => array(
                  array(
                    "title" => "View Ledger",
                    "url" => "/site-sections/budget/view_ledger.php"
                  ),
                  array(
                    "title" => "Create Expense Category",
                    "url" => "/site-sections/budget/create_expense_category.php"
                  ),
                  array(
                    "title" => "Create Income Category",
                    "url" => "/site-sections/budget/create_income_category.php"
                  ),
                  array(
                    "title" => "Create Ledger Item",
                    "url" => "/site-sections/budget/create_ledger_item.php"
                  ),
                  array(
                    "title" => "Import Ledger Items",
                    "url" => "/site-sections/budget/import_ledger_items.php"
                  )
                )
              );
            ?>
	        <!-- Right Nav Section -->
	        <ul class="middle">
	          <li class="divider"></li>
              <?php foreach ($sections as $s) : ?>
              <li class="has-dropdown"><a href="#"><?php echo $s["title"]; ?></a>
                <ul class="dropdown">
                  <?php foreach ($s["actions"] as $a) : ?>
                    <li><a href="<?php echo $a["url"]; ?>"><?php echo $a["title"]; ?></a>
                  <?php endforeach; ?>
                </ul>
              </li>
              <?php endforeach; ?>
            </ul>
	    
	        <!-- Right Nav Section -->
	        <ul class="right">
	          <li class="divider"></li>
	          <!-- search form -->
	          <!--
	          <li class="has-form">
	            <form>
	              <div class="row collapse">
	                <div class="small-8 columns">
	                  <input type="text">
	                </div>
	                <div class="small-4 columns">
	                  <a href="#" class="alert button">Search</a>
	                </div>
	              </div>s
	            </form>
	          </li>
	           -->
	          <li class="divider show-for-small"></li>
	          <li>
                <a href="profile.php"><?php echo $currentUser; ?></a>
	          </li>
	          
	          <li class="divider show-for-small"></li>
              <li class="has-form">
                <input type="submit" class="button" value="Logout" id="btnLogout" name="btnLogout" />
              </li>     
            </ul>
            
          </section>
          
        </nav>
	    
	  </LoggedInTemplate>
	  
	  <AnonymousTemplate>
	  </AnonymousTemplate>  
	</div>  
</form>
<?php endif; ?>