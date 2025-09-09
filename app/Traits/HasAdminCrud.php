<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

trait HasAdminCrud
{
    /**
     * Получить имя сущности из названия контроллера
     */
    protected function getEntityName(): string
    {
        return strtolower(str_replace('Controller', '', class_basename($this)));
    }

    /**
     * Получить сервис для сущности
     */
    protected function getService()
    {
        $serviceName = str_replace('Controller', 'Service', class_basename($this));
        return app("App\\Services\\{$serviceName}");
    }

    /**
     * Получить путь к view
     */
    protected function getViewPath(string $view): string
    {
        return "admin.{$this->getEntityName()}.{$view}";
    }

    /**
     * Получить конфигурацию сущности
     */
    protected function getEntityConfig(): array
    {
        $entityName = $this->getEntityName();
        return config("admin.entities.{$entityName}", []);
    }

    /**
     * Список записей
     */
    public function index(Request $request): View
    {
        $service = $this->getService();
        $items = $service->getAllForAdmin($request->all());
        
        return view($this->getViewPath('index'), [
            $this->getEntityName() => $items,
            'search' => $request->get('search', ''),
            'config' => $this->getEntityConfig()
        ]);
    }

    /**
     * Просмотр записи
     */
    public function show($model): View
    {
        $service = $this->getService();
        $item = $service->getForAdmin($model);
        
        return view($this->getViewPath('show'), [
            $this->getEntityName() => $item,
            'config' => $this->getEntityConfig()
        ]);
    }

    /**
     * Форма создания
     */
    public function create(): View
    {
        return view($this->getViewPath('create'), [
            'config' => $this->getEntityConfig()
        ]);
    }

    /**
     * Сохранение новой записи
     */
    public function store(Request $request): RedirectResponse
    {
        $service = $this->getService();
        $item = $service->createForAdmin($request->validated());
        
        return redirect()
            ->route("admin.{$this->getEntityName()}.show", $item)
            ->with('success', 'Запись успешно создана');
    }

    /**
     * Форма редактирования
     */
    public function edit($model): View
    {
        $service = $this->getService();
        $item = $service->getForAdmin($model);
        
        return view($this->getViewPath('edit'), [
            $this->getEntityName() => $item,
            'config' => $this->getEntityConfig()
        ]);
    }

    /**
     * Обновление записи
     */
    public function update(Request $request, $model): RedirectResponse
    {
        $service = $this->getService();
        $item = $service->updateForAdmin($model, $request->validated());
        
        return redirect()
            ->route("admin.{$this->getEntityName()}.show", $item)
            ->with('success', 'Запись успешно обновлена');
    }

    /**
     * Удаление записи
     */
    public function destroy($model): RedirectResponse
    {
        $service = $this->getService();
        $service->deleteForAdmin($model);
        
        return redirect()
            ->route("admin.{$this->getEntityName()}.index")
            ->with('success', 'Запись успешно удалена');
    }
}
