import { addAction } from '@wordpress/hooks';
import { registerModule } from '@divi/module-library';
import { createGridListModule } from './module-factory';
import gridListMetadata from './modules/grid-list-buttons/module.json';

addAction('divi.moduleLibrary.registerModuleLibraryStore.after', 'brgl.divi5Module', () => {
  const module = createGridListModule(gridListMetadata);
  const { metadata, ...moduleConfig } = module;
  registerModule(metadata, moduleConfig);
});
