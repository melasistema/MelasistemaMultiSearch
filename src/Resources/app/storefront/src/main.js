import MultiSearchPlugin from './plugins/multi-search/multi-search.plugin';

// Register your plugin via the existing PluginManager
const PluginManager = window.PluginManager;

PluginManager.register('MultiSearchPlugin', MultiSearchPlugin,"[data-multi-search]");
