import { createMongoAbility } from '@casl/ability';

export type AppAbility = ReturnType<typeof createMongoAbility>;

export const ability = createMongoAbility([]);
