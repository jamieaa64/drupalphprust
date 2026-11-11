<?php
$loader = require getcwd() . '/vendor/autoload.php';
class TracePass implements Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface {
    public function __construct(private $pass) {}
    public function process(Symfony\Component\DependencyInjection\ContainerBuilder $c): void {
        $id = 'Drupal\\canvas\\ComponentSource\\ComponentSourceManager';
        $before = $c->hasDefinition($id) ? $c->getDefinition($id)->getClass() : 'absent';
        fwrite(STDERR, 'BEFORE ' . get_class($this->pass) . ' ' . json_encode($before) . "\n");
        $this->pass->process($c);
        $after = $c->hasDefinition($id) ? $c->getDefinition($id)->getClass() : 'absent';
        fwrite(STDERR, 'AFTER ' . get_class($this->pass) . ' ' . json_encode($after) . "\n");
    }
}
class TraceBuilder extends Drupal\Core\DependencyInjection\ContainerBuilder {
    public function compile(bool $resolveEnvPlaceholders = false): void {
        $config = $this->getCompilerPassConfig();
        foreach (['BeforeOptimization', 'Optimization', 'BeforeRemoving', 'Removing', 'AfterRemoving'] as $type) {
            $get = 'get' . $type . 'Passes'; $set = 'set' . $type . 'Passes';
            $passes = []; foreach ($config->$get() as $pass) { $passes[] = new TracePass($pass); }
            $config->$set($passes);
        }
        parent::compile($resolveEnvPlaceholders);
    }
}
class TraceKernel extends Drupal\Core\DrupalKernel {
    protected function getContainerBuilder() {
        return new TraceBuilder(new Symfony\Component\DependencyInjection\ParameterBag\ParameterBag($this->getKernelParameters()));
    }
}
chdir('web');
$request = Symfony\Component\HttpFoundation\Request::create('http://localhost/');
$kernel = TraceKernel::createFromRequest($request, $loader, 'trace');
$kernel->boot();
echo "booted\n";
