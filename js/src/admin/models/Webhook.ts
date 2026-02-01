import Model from 'flarum/common/Model';

export default class Webhook extends Model {
  name = Model.attribute<string>('name');
  url = Model.attribute<string>('url');
  secret = Model.attribute<string>('secret');
  hasSecret = Model.attribute<boolean>('hasSecret');
  events = Model.attribute<string[]>('events');
  groupId = Model.attribute<number>('groupId');
  tagId = Model.attribute<number[]>('tagId');
  extraText = Model.attribute<string>('extraText');
  maxPostContentLength = Model.attribute<number>('maxPostContentLength');
  usePlainText = Model.attribute<boolean>('usePlainText');
  includeTags = Model.attribute<boolean>('includeTags');
  messageTemplate = Model.attribute<string>('messageTemplate');
  error = Model.attribute<string>('error');
  isValid = Model.attribute<boolean>('isValid');
}
