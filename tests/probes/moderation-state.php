<?php
$loader=require getcwd().'/vendor/autoload.php';chdir('web');
$request=\Symfony\Component\HttpFoundation\Request::create('http://localhost/');
$kernel=\Drupal\Core\DrupalKernel::createFromRequest($request,$loader,'prod');$kernel->boot();
$n=\Drupal\node\Entity\Node::create(['type'=>'page','title'=>'probe']);
for($i=0;$i<3;$i++) {
 echo 'state:',json_encode($n->moderation_state->value),"\n";
 echo 'field:',json_encode($n->get('moderation_state')->getValue()),"\n";
}
$kernel->shutdown();
$workflow=\Drupal::service('content_moderation.moderation_information')->getWorkflowForEntity($n);
echo 'conditional:', ($n->moderation_state->value ? $workflow->getTypePlugin()->getState($n->moderation_state->value)->id() : 'empty'),"\n";
echo 'transitions:',count(\Drupal::service('content_moderation.state_transition_validation')->getValidTransitions($n,\Drupal::currentUser())),"\n";
