<?php
  get_template_part('parts/global/html-header');
  get_template_part('parts/global/masthead');
?>

        <div class="row">
          <div class="jumbotron" style="background-image: url('//placeponi.es/1280/720')">
            <div class="jumbotron__inner">
              <div class="jumbotron__caption">
                <h1 class="jumbotron__title">Community</h1>
                <p>We've got lots of opportunity in this very community!</p>
              </div>
            </div>
          </div>
        </div>

<?php 
  if(have_posts()):
    while(have_posts()): the_post(); 
      switch(get_field("community_type")) {
        case 'image':
          $hires = wp_get_attachment_image_src(get_field("community_image"), "large");
          $full = wp_get_attachment_image_src(get_field("community_image"), "full");
          $cleansed = str_replace(site_url().'/', '', $full[0]);
?>
        <div class="row">
          <article class="post">
            <header class="post__header">
              <h2 class="post__title"><?php the_title(); ?></h2>
              <ul class="post__meta">
                <li class="post__meta__date"><i class="fa fa-pencil-square-o fa-fw"></i> Uploaded <time datetime="<?php echo get_the_date("c"); ?>"><?php echo get_the_date("jS F Y"); ?></time></li>
              </ul>
            </header>
            <div class="post__body">
              <figure class="figure">
                <a href="<?php echo $full[0]; ?>"><img src="<?php echo $hires[0]; ?>" alt="<?php the_title(); ?>"></a>
                <figcaption class="figcaption"><?php the_content(); ?></figcaption>
              </figure>
            </div>
            <div class="post__sidebar">
              <?php 
                $runners = get_field('community_creator');
                foreach($runners as $runner):
              ?>
              <aside class="profile postcard">
                <header class="profile__header">
                  <div class="profile__media">
                    <div class="profile__banner"><?php // <img src="//placeponi.es/550/200" alt=""> ?> </div>
                    <img class="profile__avatar" src="<?php echo bb_profile_avatar($runner); ?>" alt="<?php echo get_the_title($runner); ?>">
                  </div>
                  <h1 class="profile__captain"><small>Photographed by</small> <?php echo get_the_title($runner); ?></h1>
                  <ul class="profile__social-links">
                    <?php if(bb_custom_field("runner_twitter", $runner)): ?><li><a href="http://twitter.com/<?php echo bb_custom_field("runner_twitter", $runner); ?>"><i class="fa fa-fw fa-twitter"></i> <span class="hidden">Twitter</span></a></li><?php endif; ?>
                    <?php if(bb_custom_field("runner_facebook", $runner)): ?><li><a href="http://facebook.com/<?php echo bb_custom_field("runner_facebook", $runner); ?>"><i class="fa fa-fw fa-facebook"></i> <span class="hidden">Facebook</span></a></li><?php endif; ?>
                  </ul>
                </header>
                <div class="profile__biography postcard__data">
                  <p><?php echo bb_profile_biography($runner); ?></p>
                </div>
              </aside>
              <?php 
                endforeach;
              ?>
              <?php 
                $camera = community_camera_metadata($cleansed);
                if($camera['date'] || ($camera['make'] && $camera['model']) || $camera['exposure'] || $camera['aperture'] || $camera['iso']):
              ?>
              <aside class="camera-meta postcard">
                <div class="postcard__title">
                  <h1>Meta</h1>
                </div>
                <div class="postcard__data">
                  <p>Photographed<?php if($camera['date']): ?> on <strong><?php echo date("jS F Y \a\\t g:ia", strtotime($camera['date'])); ?></strong><?php endif; if($camera['make'] && $camera['model']): ?> using a <?php echo $camera['make']; ?> <?php echo $camera['model']; ?><?php endif; ?>.</p>
                  <p>
                    <?php if($camera['exposure']): ?><abbr title="<?php echo $camera['exposure']; ?> exposure"><?php echo $camera['exposure']; ?></abbr><?php endif; ?>
                    <?php if($camera['aperture']): ?><abbr title="<?php echo $camera['aperture']; ?> aperture"><?php echo $camera['aperture']; ?></abbr><?php endif; ?>
                    <?php if($camera['iso']): ?><abbr title="ISO <?php echo $camera['iso']; ?>">ISO <?php echo $camera['iso']; ?></abbr><?php endif; ?>
                  </p>
                </div>
              </aside>
              <?php 
                endif;
              ?>
            </div>
          </article>
        </div>
<?php
        break;
        case 'video':
?>
        <div class="row">
          <article class="post">
            <header class="post__header">
              <h2 class="post__title"><?php the_title(); ?></h2>
              <ul class="post__meta">
                <li class="post__meta__date"><i class="fa fa-pencil-square-o fa-fw"></i> Uploaded <time datetime="<?php echo get_the_date("c"); ?>"><?php echo get_the_date("jS F Y"); ?></time></li>
              </ul>
            </header>
            <div class="post__body">
              <figure class="figure">
                <?php echo get_field("community_video"); ?>
                <figcaption class="figcaption"><?php the_content(); ?></figcaption>
              </figure>
            </div>
            <div class="post__sidebar">
              <?php 
                $runners = get_field('community_creator');
                foreach($runners as $runner):
              ?>
              <aside class="profile postcard">
                <header class="profile__header">
                  <div class="profile__media">
                    <div class="profile__banner"><?php // <img src="//placeponi.es/550/200" alt=""> ?> </div>
                    <img class="profile__avatar" src="<?php echo bb_profile_avatar($runner); ?>" alt="<?php echo get_the_title($runner); ?>">
                  </div>
                  <h1 class="profile__captain"><small>Recorded by</small> <?php echo get_the_title($runner); ?></h1>
                  <ul class="profile__social-links">
                    <?php if(bb_custom_field("runner_twitter", $runner)): ?><li><a href="http://twitter.com/<?php echo bb_custom_field("runner_twitter", $runner); ?>"><i class="fa fa-fw fa-twitter"></i> <span class="hidden">Twitter</span></a></li><?php endif; ?>
                    <?php if(bb_custom_field("runner_facebook", $runner)): ?><li><a href="http://facebook.com/<?php echo bb_custom_field("runner_facebook", $runner); ?>"><i class="fa fa-fw fa-facebook"></i> <span class="hidden">Facebook</span></a></li><?php endif; ?>
                  </ul>
                </header>
                <div class="profile__biography postcard__data">
                  <p><?php echo bb_profile_biography($runner); ?></p>
                </div>
              </aside>
              <?php 
                endforeach;
              ?>
            </div>
          </article>
        </div>
<?php
        break;
        case 'text':
        default:
?>
        <div class="row">
          <article class="post">
            <header class="post__header">
              <h2 class="post__title"><?php the_title(); ?></h2>
              <ul class="post__meta">
                <li class="post__meta__date"><i class="fa fa-pencil-square-o fa-fw"></i> Uploaded <time datetime="<?php echo get_the_date("c"); ?>"><?php echo get_the_date("jS F Y"); ?></time></li>
              </ul>
            </header>
            <div class="post__body">
              <?php the_content(); ?>
            </div>
            <div class="post__sidebar">
              <?php 
                $runners = get_field('community_creator');
                foreach($runners as $runner):
              ?>
              <aside class="profile postcard">
                <header class="profile__header">
                  <div class="profile__media">
                    <div class="profile__banner"><?php // <img src="//placeponi.es/550/200" alt=""> ?> </div>
                    <img class="profile__avatar" src="<?php echo bb_profile_avatar($runner); ?>" alt="<?php echo get_the_title($runner); ?>">
                  </div>
                  <h1 class="profile__captain"><small>Written by</small> <?php echo get_the_title($runner); ?></h1>
                  <ul class="profile__social-links">
                    <?php if(bb_custom_field("runner_twitter", $runner)): ?><li><a href="http://twitter.com/<?php echo bb_custom_field("runner_twitter", $runner); ?>"><i class="fa fa-fw fa-twitter"></i> <span class="hidden">Twitter</span></a></li><?php endif; ?>
                    <?php if(bb_custom_field("runner_facebook", $runner)): ?><li><a href="http://facebook.com/<?php echo bb_custom_field("runner_facebook", $runner); ?>"><i class="fa fa-fw fa-facebook"></i> <span class="hidden">Facebook</span></a></li><?php endif; ?>
                  </ul>
                </header>
                <div class="profile__biography postcard__data">
                  <p><?php echo bb_profile_biography($runner); ?></p>
                </div>
              </aside>
              <?php 
                endforeach;
              ?>
            </div>
          </article>
        </div>
<?php
        break;
      }
    endwhile;
  endif; 
?>

<!-- index.php -->

<?php
  get_template_part('parts/global/footer');
  get_template_part('parts/global/html-footer');
?>